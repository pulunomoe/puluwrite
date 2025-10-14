<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Dto\UpdatePasswordDto;
use App\Exception\TwigException;
use App\Exception\UserException;
use App\Service\UserService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

readonly class UserController extends Controller
{
    /**
     * @throws TwigException
     */
    public function login(
        Response $response,
    ): ResponseInterface {
        return $this->render($response, 'users/login.twig');
    }

    public function loginPost(
        ServerRequest $request,
        Response $response,
        UserService $userService,
    ): ResponseInterface {
        try {
            $userService->login(new LoginDto($request));

            return $this->redirectWithSuccess($response, '/', 'Login successful');
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/login', $ex->getMessage());
        }
    }

    public function logout(
        Response $response,
    ): ResponseInterface {
        session_destroy();

        return $this->redirectWithSuccess($response, '/login', 'Logout successful');
    }

    /**
     * @throws TwigException
     */
    public function password(
        Response $response,
    ): ResponseInterface {
        return $this->render($response, 'users/password.twig');
    }

    public function passwordPost(
        ServerRequest $request,
        Response $response,
        UserService $userService,
    ): ResponseInterface {
        try {
            $userService->updatePassword(new UpdatePasswordDto($request));

            return $this->redirectWithSuccess($response, '/', 'Password changed');
        } catch (UserException $ex) {
            return $this->redirectWithError($response, '/password', $ex->getMessage());
        }
    }
}
