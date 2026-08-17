<?php

declare(strict_types=1);

namespace App\Clientes\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClienteNotFoundException extends HttpException
{
    public function __construct(string $message = "El cliente no existe")
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}