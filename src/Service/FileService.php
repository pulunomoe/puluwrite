<?php

namespace App\Service;

use App\Dto\CreateFileDto;
use App\Dto\UpdateFileContentDto;
use App\Dto\UpdateFileDto;
use App\Exception\UserException;
use App\Repository\FileRepository;
use App\Repository\FolderRepository;
use Ulid\Ulid;

readonly class FileService
{
    public function __construct(
        private FileRepository $fileRepository,
        private FolderRepository $folderRepository,
    ) {}

    /**
     * @throws UserException
     */
    public function readAllByFolderId(
        string $folderId,
    ): array {
        return $this->fileRepository->selectAllByFolderId($folderId);
    }

    /**
     * @throws UserException
     */
    public function readOne(
        string $id,
    ): array {
        $file = $this->fileRepository->selectOneById($id);
        if (empty($file) || $file['user_id'] != $_SESSION['user']['id']) {
            throw new UserException('File not found');
        }

        return $file;
    }

    /**
     * @throws UserException
     */
    public function create(
        CreateFileDto $createFileDto,
    ): string {
        $this->validateFolderOwnership($createFileDto->folderId);

        $id = Ulid::generate(true);
        $this->fileRepository->insert(
            $id,
            $createFileDto->folderId,
            $createFileDto->title,
        );

        return $id;
    }

    /**
     * @throws UserException
     */
    public function update(
        UpdateFileDto $updateFileDto,
    ): string {
        $file = $this->readOne($updateFileDto->id);

        $this->fileRepository->update(
            $updateFileDto->id,
            $updateFileDto->title,
        );

        return $file['folder_id'];
    }

    /**
     * @throws UserException
     */
    public function updateContent(
        UpdateFileContentDto $updateFileContentDto,
    ): void {
        $this->readOne($updateFileContentDto->id);

        $this->fileRepository->updateContent(
            $updateFileContentDto->id,
            $updateFileContentDto->content,
        );
    }

    /**
     * @throws UserException
     */
    public function delete(
        string $id,
    ): void {
        $this->readOne($id);

        $this->fileRepository->delete($id);
    }

    /**
     * @throws UserException
     */
    private function validateFolderOwnership(
        string $folderId,
    ): void {
        $folder = $this->folderRepository->selectOneByIdAndUserId($folderId, $_SESSION['user']['id']);
        if (empty($folder)) {
            throw new UserException('Folder not found');
        }
    }
}
