<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class UpdatePasswordDto
{
    public string $oldPassword;
    public string $newPassword;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $oldPassword = $request->getParam('oldPassword');
        $newPassword = $request->getParam('newPassword');
        $confirmPassword = $request->getParam('confirmPassword');

        if (empty($oldPassword)) {
            throw new UserException('Old password is required');
        }

        if (empty($newPassword)) {
            throw new UserException('New password is required');
        }

        if (empty($confirmPassword)) {
            throw new UserException('Password confirmation is required');
        }

        if (!password_verify($confirmPassword, password_hash($newPassword, PASSWORD_DEFAULT))) {
            throw new UserException('Password confirmation mismatch');
        }

        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }
}
