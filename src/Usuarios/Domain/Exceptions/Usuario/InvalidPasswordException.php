<?php

declare(strict_types=1);

namespace App\Usuarios\Domain\Exceptions\Usuario;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidPasswordException extends HttpException
{
    public function __construct(string $message = 'La contraseña debe tener al menos 6 caracteres')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}