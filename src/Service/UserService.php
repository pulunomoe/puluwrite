<?php

namespace App\Service;

use App\Dto\LoginDto;
use App\Dto\UpdatePasswordDto;
use App\Exception\UserException;
use App\Repository\UserRepository;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @throws UserException
     */
    public function login(
        LoginDto $loginDto,
    ): void {
        $user = $this->userRepository->selectOneByEmail($loginDto->email);

        if (empty($user)) {
            throw new UserException('Invalid email or password');
        }

        if (!password_verify($loginDto->password, $user['password'])) {
            throw new UserException('Invalid email or password');
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
    }

    /**
     * @throws UserException
     */
    public function updatePassword(
        UpdatePasswordDto $updatePasswordDto,
    ): void {
        $user = $this->userRepository->selectOneByEmail($_SESSION['user']['email']);

        if (!password_verify($updatePasswordDto->oldPassword, $user['password'])) {
            throw new UserException('Invalid old password');
        }

        $this->userRepository->updatePassword(
            $_SESSION['user']['id'],
            password_hash($updatePasswordDto->newPassword, PASSWORD_DEFAULT),
        );
    }
}
