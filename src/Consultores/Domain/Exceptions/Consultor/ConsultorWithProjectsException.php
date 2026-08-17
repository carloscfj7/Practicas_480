<?php

declare(strict_types=1);

namespace App\Consultores\Domain\Exceptions\Consultor;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConsultorWithProjectsException extends HttpException
{
    public function __construct(string $message = 'El consultor no puede ser eliminado ya que tiene proyectos')
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);

    }
}