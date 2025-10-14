<?php

namespace App\Repository;

readonly class UserRepository extends Repository
{
    public function selectOneByEmail(
        string $email,
    ): array|false {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function updatePassword(
        string $id,
        string $password,
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET password = :password WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
    }
}
