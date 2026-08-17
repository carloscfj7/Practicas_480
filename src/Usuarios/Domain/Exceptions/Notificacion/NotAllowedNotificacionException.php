<?php

declare(strict_types=1);

namespace App\Usuarios\Domain\Exceptions\Notificacion;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotAllowedNotificacionException extends HttpException
{
    public function __construct($message = ' No tiene permiso para acceder a la notifiacion indicada')
    {
        parent::__construct(Response::HTTP_FORBIDDEN, $message);
    }
}