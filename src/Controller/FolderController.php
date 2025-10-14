<?php

namespace App\Controller;

use App\Dto\CreateFolderDto;
use App\Dto\UpdateFolderDto;
use App\Exception\TwigException;
use App\Exception\UserException;
use App\Service\FileService;
use App\Service\FolderService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

readonly class FolderController extends Controller
{
    /**
     * @throws TwigException
     */
    public function view(
        ?string $id,
        Response $response,
        FolderService $folderService,
        FileService $fileService,
    ): ResponseInterface {
        try {
            if (!empty($id)) {
                $folder = $folderService->readOne($id);
                $parents = $folderService->readAllParentsRecursive($id);
                $files = $fileService->readAllByFolderId($id);
            }
            $subfolders = $folderService->readAllByParentId($id);

            return $this->render($response, 'folders/view.twig', [
                'folder' => $folder ?? null,
                'parents' => $parents ?? null,
                'subfolders' => $subfolders,
                'files' => $files ?? null,
            ]);
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/', $ex->getMessage());
        }
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        FolderService $folderService,
    ): ResponseInterface {
        try {
            $folderId = $folderService->create(new CreateFolderDto($request));

            return $this->redirectWithSuccess($response, '/folders/' . $folderId, 'Folder created');
        } catch (UserException $ex) {
            $parentFolderId = $request->getParam('parentId');

            if (empty($parentFolderId)) {
                return $this->redirectWithError($response, '/', $ex->getMessage());
            } else {
                return $this->redirectWithError($response, '/folders/' . $parentFolderId, $ex->getMessage());
            }
        }
    }

    public function updatePost(
        ServerRequest $request,
        Response $response,
        FolderService $folderService,
    ): ResponseInterface {
        $parentFolderId = $request->getParam('parentId');
        try {
            $parentFolderId = $folderService->update(new UpdateFolderDto($request));

            if (empty($parentFolderId)) {
                return $this->redirectWithSuccess($response, '/', 'Folder renamed');
            } else {
                return $this->redirectWithSuccess($response, '/folders/' . $parentFolderId, 'Folder renamed');
            }
        } catch (UserException $ex) {
            if (empty($parentFolderId)) {
                return $this->redirectWithError($response, '/', $ex->getMessage());
            } else {
                return $this->redirectWithError($response, '/folders/' . $parentFolderId, $ex->getMessage());
            }
        }
    }

    public function deletePost(
        ServerRequest $request,
        Response $response,
        FolderService $folderService,
    ): ResponseInterface {
        $parentFolderId = $request->getParam('parentId');
        try {
            $folderService->delete($request->getParam('id'));

            if (empty($parentFolderId)) {
                return $this->redirectWithSuccess($response, '/', 'Folder deleted');
            } else {
                return $this->redirectWithSuccess($response, '/folders/' . $parentFolderId, 'Folder deleted');
            }
        } catch (UserException $ex) {
            if (empty($parentFolderId)) {
                return $this->redirectWithError($response, '/', $ex->getMessage());
            } else {
                return $this->redirectWithError($response, '/folders/' . $parentFolderId, $ex->getMessage());
            }
        }
    }
}
