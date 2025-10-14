<?php

namespace App\Repository;

readonly class FolderRepository extends Repository
{
    public function selectAllByUserId(
        string $userId,
    ): array {
        $stmt = $this->pdo->prepare('SELECT * FROM folders WHERE parent_id IS NULL AND user_id = :userId');
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function selectAllByParentId(
        string $parentId,
    ): array {
        $stmt = $this->pdo->prepare('SELECT * FROM folders WHERE parent_id = :parentId');
        $stmt->bindValue(':parentId', $parentId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function selectAllParentsRecursive(
        string $id,
    ): array {
        $stmt = $this->pdo->prepare(
            'WITH RECURSIVE ancestors AS (
                SELECT id, parent_id, name, 0 AS level
                FROM folders
                WHERE id = :id
                UNION ALL
                SELECT f.id, f.parent_id, f.name, a.level + 1
                FROM folders f
                         JOIN ancestors a ON f.id = a.parent_id
            )
            SELECT id, name
            FROM ancestors
            ORDER BY level DESC',
        );
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function selectOneByIdAndUserId(
        string $id,
        string $userId,
    ): array {
        $stmt = $this->pdo->prepare('SELECT * FROM folders WHERE id = :id AND user_id = :userId LIMIT 1');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(
        string $id,
        string $userId,
        ?string $parentId,
        string $name,
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO folders (id, user_id, parent_id, name) VALUES (:id, :userId, :parentId, :name)',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':userId', $userId);
        $stmt->bindValue(':parentId', $parentId);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
    }

    public function update(
        string $id,
        string $name,
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE folders SET name = :name WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
    }

    public function delete(
        string $id,
    ): void {
        $stmt = $this->pdo->prepare(
            'DELETE FROM folders WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
}
