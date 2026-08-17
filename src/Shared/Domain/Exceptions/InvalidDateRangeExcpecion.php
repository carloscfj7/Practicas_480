<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidDateRangeExcpecion extends HttpException
{
    public function __construct(string $message = 'La de fin no puede ser menor a la fecha de inicio')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}