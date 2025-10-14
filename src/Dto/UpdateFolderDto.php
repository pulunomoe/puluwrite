<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class UpdateFolderDto
{
    public string $id;
    public string $name;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $id = $request->getParam('id');
        $name = $request->getParam('name');

        if (empty($id)) {
            throw new UserException('Folder not found');
        }

        if (empty($name)) {
            throw new UserException('Folder name is required');
        }

        $this->id = $id;
        $this->name = $name;
    }
}
