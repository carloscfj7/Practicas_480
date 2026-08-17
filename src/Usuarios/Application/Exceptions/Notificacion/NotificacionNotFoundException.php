<?php
declare(strict_types=1);

namespace App\Usuarios\Application\Exceptions\Notificacion;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotificacionNotFoundException extends HttpException
{
    public function __construct($message = "La notificacion no existe")
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message);
    }
}