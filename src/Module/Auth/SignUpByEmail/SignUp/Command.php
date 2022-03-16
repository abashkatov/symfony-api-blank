<?php

declare(strict_types=1);

namespace App\Module\Auth\SignUpByEmail\SignUp;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    private const PASSWORD_REGEX = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/';

    #[Assert\Email]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Regex(self::PASSWORD_REGEX)]
    private string $password;

    public function __construct(string $email = '', string $password = '')
    {
        $this->email    = \mb_strtolower(trim($email));
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
