<?php

declare(strict_types=1);

namespace App\Shared\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequiredDataException extends HttpException
{
    public function __construct(string $message = "Faltan datos obligatorios")
    {
        parent::__construct(Response::HTTP_BAD_REQUEST,$message);
    }
}