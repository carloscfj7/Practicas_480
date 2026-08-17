<?php

declare(strict_types=1);

namespace App\Proyectos\Domain\Exceptions\Proyecto;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmptyConsultoresException extends HttpException
{
    public function __construct(string $message = "No se puede eliminar el conuslotr ya que es el ultimo consultor del proyecto")
    {
        parent::__construct(Response::HTTP_CONFLICT, $message);
    }
}