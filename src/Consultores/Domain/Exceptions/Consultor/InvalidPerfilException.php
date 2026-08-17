<?php

declare(strict_types=1);

namespace App\Consultores\Domain\Exceptions\Consultor;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidPerfilException extends HttpException
{
    public function __construct($message = 'El perfil introducido no es valido')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}