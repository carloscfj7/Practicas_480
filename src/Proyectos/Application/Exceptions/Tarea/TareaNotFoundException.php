<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Exceptions\Tarea;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TareaNotFoundException extends HttpException
{
    public function __construct($message = 'Tarea no encontrada')
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}