<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class CreateFolderDto
{
    public ?string $parentId;
    public string $name;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $parentId = $request->getParam('parentId');
        $name = $request->getParam('name');

        if (empty($parentId)) {
            $parentId = null;
        }

        if (empty($name)) {
            throw new UserException('Folder name is required');
        }

        $this->parentId = $parentId;
        $this->name = $name;
    }
}
