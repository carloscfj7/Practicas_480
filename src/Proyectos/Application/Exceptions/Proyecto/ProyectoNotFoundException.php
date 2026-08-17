<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Exceptions\Proyecto;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProyectoNotFoundException extends HttpException
{
    public function __construct(string $message = "Proyecto no encontrado"){
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}