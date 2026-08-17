<?php

declare(strict_types=1);

namespace App\Consultores\Application\Exceptions\Disponibilidad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DisponibilidadNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_FOUND, "Disponibilidad no encontrada");
    }
}