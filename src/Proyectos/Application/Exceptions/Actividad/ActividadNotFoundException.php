<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Exceptions\Actividad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActividadNotFoundException extends HttpException
{
    public function __construct(string $message = "La actividad no existe")
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}