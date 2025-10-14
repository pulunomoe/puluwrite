<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class UpdateFileDto
{
    public string $id;
    public string $title;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $id = $request->getParam('id');
        $title = $request->getParam('title');

        if (empty($id)) {
            throw new UserException('File not found');
        }

        if (empty($title)) {
            throw new UserException('Title is required');
        }

        $this->id = $id;
        $this->title = $title;
    }
}
