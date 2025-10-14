<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class CreateFileDto
{
    public string $folderId;
    public string $title;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $folderId = $request->getParam('folderId');
        $title = $request->getParam('title');

        if (empty($folderId)) {
            throw new UserException('Folder not found');
        }

        if (empty($title)) {
            throw new UserException('Title is required');
        }

        $this->folderId = $folderId;
        $this->title = $title;
    }
}
