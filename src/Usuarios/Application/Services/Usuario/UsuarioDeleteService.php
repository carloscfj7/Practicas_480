<?php
declare(strict_types=1);

namespace App\Usuarios\Application\Services\Usuario;

use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UsuarioDeleteService
{
    public function __construct(private UsuarioRepositoryInterface $usuarioRepository)
    {
    }

    public function __invoke(Usuario $usuario): JsonResponse{
        $this->usuarioRepository->remove($usuario);
        return new JsonResponse(['message' => 'Usuario eliminado correctamente'], Response::HTTP_OK);
    }

}