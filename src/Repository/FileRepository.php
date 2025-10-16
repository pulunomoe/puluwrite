<?php

namespace App\Repository;

readonly class FileRepository extends Repository
{
    public function selectAllByFolderId(
        string $folderId,
    ): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, id, folder_id, title, updated FROM files WHERE folder_id = :folderId",
        );
        $stmt->bindValue(':folderId', $folderId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function selectOneById(
        string $id,
    ): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM files_view WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function insert(
        string $id,
        string $folderId,
        string $title,
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO files (id, folder_id, title, updated) VALUES (:id, :folderId, :title, :updated)',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':folderId', $folderId);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':updated', date('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function update(
        string $id,
        string $title,
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE files SET title = :title, updated = :updated WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':updated', date('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function updateContent(
        string $id,
        string $content,
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE files SET content = :content, size = :size, updated = :updated WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':size', strlen($content));
        $stmt->bindValue(':updated', date('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function delete(
        string $id,
    ): void {
        $stmt = $this->pdo->prepare(
            'DELETE FROM files WHERE id = :id',
        );
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
}
