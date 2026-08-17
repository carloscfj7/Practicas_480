<?php

declare(strict_types=1);

namespace App\Proyectos\Domain\Exceptions\Proyecto;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistentProyectoException extends HttpException
{
    public function __construct($message = "Ya existe un proyecto con el nombre proporcionado")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}