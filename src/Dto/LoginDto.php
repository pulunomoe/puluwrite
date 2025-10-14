<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class LoginDto
{
    public string $email;
    public string $password;

    /**
     * @throws UserException
     */
    public function __construct(ServerRequest $request)
    {
        $email = $request->getParam('email');
        $password = $request->getParam('password');

        if (empty($email) || empty($password)) {
            throw new UserException('Email and password are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserException('Invalid email format');
        }

        $this->email = strtolower($email);
        $this->password = $password;
    }
}
