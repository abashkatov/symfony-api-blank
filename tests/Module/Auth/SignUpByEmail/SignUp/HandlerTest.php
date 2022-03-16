<?php

namespace App\Tests\Module\Auth\SignUpByEmail\SignUp;

use App\Exception\InvalidParamsException;
use App\Module\Auth\SignUpByEmail\SignUp\Command;
use App\Module\Auth\SignUpByEmail\SignUp\Handler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HandlerTest extends KernelTestCase
{
    /**
     * @return array<string, array<string, string>>
     */
    public function handleWrongEmailsProvider(): array
    {
        return [
            'wrong domain' => [
                'email' => 'email@domain',
            ],
            'wrong format' => [
                'email' => 'email-domain.ru',
            ],
            'empty account' => [
                'email' => '@domain.ru',
            ],
        ];
    }

    /** @dataProvider handleWrongEmailsProvider */
    public function testHandleWrongEmails(string $email): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var Handler $handler */
        $handler = $container->get(Handler::class);
        $command = new Command($email, 'Password1!');
        $this->expectException(InvalidParamsException::class);
        try {
            $handler->handle($command);
        } catch (InvalidParamsException $e) {
            $this->assertCount(1, $e->getErrors());
            $this->assertEquals('This value is not a valid email address.', $e->getErrors()->get(0)->getMessage());
            throw $e;
        }
    }

    public function testHandleCorrectEmail(): void
    {
        $email = 'email@email.com';
        self::bootKernel();
        $container = static::getContainer();
        /** @var Handler $handler */
        $handler = $container->get(Handler::class);
        $command = new Command($email, 'Password1!');
        $user = $handler->handle($command);
        $this->assertEquals($email, $user->getEmail());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function handleWrongPasswordProvider(): array
    {
        return [
            'blank password' => [
                'password' => '',
                'message' => 'This value should not be blank.',
            ],
            'password without digital' => [
                'password' => '!Password',
                'message' => 'This value is not valid.',
            ],
            'password without special chars' => [
                'password' => '9Password',
                'message' => 'This value is not valid.',
            ],
            'password without uppercase letters' => [
                'password' => '!9password',
                'message' => 'This value is not valid.',
            ],
            'password without lowercase letters' => [
                'password' => '!9PASSWORD',
                'message' => 'This value is not valid.',
            ],
            'password is less then 6 symbols' => [
                'password' => '!9pass',
                'message' => 'This value is not valid.',
            ],
        ];
    }

    /** @dataProvider handleWrongPasswordProvider */
    public function testHandleWrongPassword(string $password, string $message): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var Handler $handler */
        $handler = $container->get(Handler::class);
        $command = new Command('email@email.com', $password);
        $this->expectException(InvalidParamsException::class);
        try {
            $handler->handle($command);
        } catch (InvalidParamsException $e) {
            $this->assertCount(1, $e->getErrors());
            $this->assertEquals($message, $e->getErrors()->get(0)->getMessage());
            throw $e;
        }
    }

    public function testHandleCorrectPassword(): void
    {
        $password = 'Password1!';
        self::bootKernel();
        $container = static::getContainer();
        /** @var Handler $handler */
        $handler = $container->get(Handler::class);
        $command = new Command('email@email.com', $password);
        $user = $handler->handle($command);
        $this->assertNotEmpty($user->getPassword());
        $this->assertNotEquals($password, $user->getPassword());
    }
}
