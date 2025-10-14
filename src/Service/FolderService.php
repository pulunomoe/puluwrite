<?php

namespace App\Service;

use App\Dto\CreateFolderDto;
use App\Dto\UpdateFolderDto;
use App\Exception\UserException;
use App\Repository\FolderRepository;
use Ulid\Ulid;

readonly class FolderService
{
    public function __construct(
        private FolderRepository $folderRepository,
    ) {}

    public function readAllByParentId(
        ?string $parentId,
    ): array {
        if (empty($parentId)) {
            return $this->folderRepository->selectAllByUserId($_SESSION['user']['id']);
        }

        return $this->folderRepository->selectAllByParentId($parentId);
    }

    public function readAllParentsRecursive(
        string $id,
    ): array {
        return $this->folderRepository->selectAllParentsRecursive($id);
    }

    /**
     * @throws UserException
     */
    public function readOne(
        string $id,
    ): array {
        $folder = $this->folderRepository->selectOneByIdAndUserId($id, $_SESSION['user']['id']);
        if (empty($folder)) {
            throw new UserException('Folder not found');
        }

        return $folder;
    }

    /**
     * @throws UserException
     */
    public function create(
        CreateFolderDto $createFolderDto,
    ): string {
        if (!empty($createFolderDto->parentId)) {
            $parentFolder = $this->folderRepository->selectOneByIdAndUserId(
                $createFolderDto->parentId,
                $_SESSION['user']['id'],
            );
            if (empty($parentFolder)) {
                throw new UserException('Parent folder not found');
            }
        }

        $id = Ulid::generate(true);
        $this->folderRepository->create(
            $id,
            $_SESSION['user']['id'],
            $createFolderDto->parentId,
            $createFolderDto->name,
        );

        return $id;
    }

    /**
     * @throws UserException
     */
    public function update(
        UpdateFolderDto $updateFolderDto,
    ): ?string {
        $folder = $this->readOne($updateFolderDto->id);

        return $folder['parent_id'];
    }

    /**
     * @throws UserException
     */
    public function delete(
        string $id,
    ): void {
        $this->readOne($id);

        $this->folderRepository->delete($id);
    }
}
