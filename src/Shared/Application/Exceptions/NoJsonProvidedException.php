<?php

declare(strict_types=1);

namespace App\Shared\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NoJsonProvidedException extends HttpException
{
    public function __construct(string $message = 'Es obligatorio proporcionar un Json con los datos necesarios.')
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}