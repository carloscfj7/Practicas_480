<?php

declare(strict_types=1);

namespace App\Consultores\Domain\Exceptions\Habilidad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidNivelException extends HttpException
{
    public function __construct($message = 'El nivel introducido no es valido')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}