<?php

declare(strict_types=1);

namespace App\Consultores\Domain\Exceptions\Habilidad;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistentHabilidadException extends HttpException
{
    public function __construct($message = "Ya esxite una habilidad con estas caracteristicas")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message, );
    }
}