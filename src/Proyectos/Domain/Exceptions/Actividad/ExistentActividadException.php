<?php

declare(strict_types=1);

namespace App\Proyectos\Domain\Exceptions\Actividad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistentActividadException extends HttpException
{
    public function __construct(string $message = "La actividad ya existe")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}