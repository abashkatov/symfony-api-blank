<?php

declare(strict_types=1);

namespace App\Module\Auth\SignUpByEmail\SignUp;

use App\Entity\User;
use App\Exception\InvalidParamsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;

    private UserPasswordHasherInterface $hasher;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher
    ) {
        $this->validator = $validator;
        $this->hasher = $hasher;
    }

    public function handle(Command $command): User
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid registration params", $errors);
        }
        $user = (new User())
            ->setEmail($command->getEmail());
        $hashedPassword = $this->hasher->hashPassword($user, $command->getPassword());
        $user->setPassword($hashedPassword);

        return $user;
    }
}
