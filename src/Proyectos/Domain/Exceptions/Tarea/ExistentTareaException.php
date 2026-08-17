<?php

declare(strict_types=1);

namespace App\Proyectos\Domain\Exceptions\Tarea;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistentTareaException extends HttpException
{
    public function __construct(string $message = "Ya existe una tarea en el proyecto on el nombre proporcionado")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}