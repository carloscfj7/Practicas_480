<?php

declare(strict_types=1);

namespace App\Clientes\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClienteWithProjectsException extends HttpException
{
    public function __construct(string $message = 'EL cliente no puede ser eliminado ya que tiene proyectos')
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}