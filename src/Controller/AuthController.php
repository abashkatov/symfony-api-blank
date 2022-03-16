<?php
declare(strict_types=1);

namespace App\Controller;

use App\Module\Auth\SignUpByEmail\SignUp\Command;
use App\Module\Auth\SignUpByEmail\SignUp\Handler as SignUpHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
    private Serializer             $serializer;

    private EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/registration', name: 'app_registration', methods: 'POST')]
    public function index(SignUpHandler $handler, Request $request): Response
    {
        $command = $this->serializer->denormalize($request->request->all(), Command::class);
        $user = $handler->handle($command);
        $this->em->persist($user);
        $this->em->flush();
        $data = $this->serializer->normalize($user);
        return $this->json($data);
    }
}
