<?php

declare(strict_types=1);

namespace App\Shared\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidDateTimeException extends HttpException
{
    public function __construct(string $message = 'La fecha introducida no es valida (AAAA-MM-DD HH:MM:SS)')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}