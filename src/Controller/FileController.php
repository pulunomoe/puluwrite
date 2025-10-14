<?php

namespace App\Controller;

use App\Dto\CreateFileDto;
use App\Dto\UpdateFileContentDto;
use App\Dto\UpdateFileDto;
use App\Exception\TwigException;
use App\Exception\UserException;
use App\Service\FileService;
use App\Service\FolderService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

readonly class FileController extends Controller
{
    /**
     * @throws TwigException
     */
    public function view(
        string $id,
        Response $response,
        FileService $fileService,
        FolderService $folderService,
    ): ResponseInterface {
        try {
            $file = $fileService->readOne($id);
            $parents = $folderService->readAllParentsRecursive($file['folder_id']);

            return $this->render($response, 'files/view.twig', [
                'file' => $file,
                'parents' => $parents,
            ]);
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/', $ex->getMessage());
        }
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        FileService $fileService,
    ): ResponseInterface {
        try {
            $fileId = $fileService->create(new CreateFileDto($request));

            return $this->redirectWithSuccess($response, '/files/' . $fileId, 'File created');
        } catch (UserException $ex) {
            $folderId = $request->getParam('folderId');

            return $this->redirectWithError($response, '/folders/' . $folderId, $ex->getMessage());
        }
    }

    public function updatePost(
        ServerRequest $request,
        Response $response,
        FileService $fileService,
    ): ResponseInterface {
        try {
            $folderId = $fileService->update(new UpdateFileDto($request));

            return $this->redirectWithSuccess($response, '/folders/' . $folderId, 'File renamed');
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/folders/' . $request->getParam('folderId'), $ex->getMessage());
        }
    }

    public function updateContentPost(
        ServerRequest $request,
        Response $response,
        FileService $fileService,
    ): ResponseInterface {
        try {
            $fileService->updateContent(new UpdateFileContentDto($request));

            return $response->withStatus(200);
        } catch (UserException) {
            return $response->withStatus(400);
        }
    }

    public function deletePost(
        ServerRequest $request,
        Response $response,
        FileService $fileService,
    ): ResponseInterface {
        try {
            $file = $fileService->readOne($request->getParam('id'));
            $fileService->delete($request->getParam('id'));

            return $this->redirectWithSuccess($response, '/folders/' . $file['folder_id'], 'File deleted');
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/folders/' . $request->getParam('folderId'), $ex->getMessage());
        }
    }
}
