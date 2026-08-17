<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Exceptions\Usuario;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsException extends HttpException
{
    public function __construct(string $messsage = 'Las credenciales introducidas no son correctas.')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $messsage);
    }
}