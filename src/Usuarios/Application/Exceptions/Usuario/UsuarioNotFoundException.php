<?php
declare(strict_types=1);

namespace App\Usuarios\Application\Exceptions\Usuario;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsuarioNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_FOUND, 'El usuario no existe.');
    }
}
