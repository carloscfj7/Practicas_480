<?php
declare(strict_types=1);

namespace App\Usuarios\Application\Exceptions\Usuario;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidEmailException extends HttpException
{
    public function __construct($message = 'El email proporcionado no es válido' )
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}