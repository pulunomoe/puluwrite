<?php

namespace App\Dto;

use App\Exception\UserException;
use Slim\Http\ServerRequest;

readonly class UpdateFileContentDto
{
    public string $id;
    public string $content;

    /**
     * @throws UserException
     */
    public function __construct(
        ServerRequest $request,
    ) {
        $id = $request->getParam('id');
        $content = $request->getParam('content');

        if (empty($id)) {
            throw new UserException('File not found');
        }

        $this->id = $id;
        $this->content = $content;
    }
}
