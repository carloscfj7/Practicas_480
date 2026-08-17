<?php

declare(strict_types=1);

namespace App\Consultores\Domain\Exceptions\Disponibilidad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistentDisponibilidadException extends HttpException
{
    public function __construct(string $message = "La disponibilidad ya existe")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}