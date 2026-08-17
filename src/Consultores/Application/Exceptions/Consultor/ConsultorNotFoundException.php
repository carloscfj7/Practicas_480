<?php

declare(strict_types=1);

namespace App\Consultores\Application\Exceptions\Consultor;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConsultorNotFoundException extends HttpException
{
    public function __construct(string $message = 'El consultor no existe')
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}