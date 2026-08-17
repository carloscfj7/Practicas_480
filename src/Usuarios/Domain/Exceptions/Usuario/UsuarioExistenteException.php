<?php

declare(strict_types=1);

namespace App\Usuarios\Domain\Exceptions\Usuario;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsuarioExistenteException extends HttpException
{
    public function __construct(string $message="Ya existe un usuario con ese email")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}