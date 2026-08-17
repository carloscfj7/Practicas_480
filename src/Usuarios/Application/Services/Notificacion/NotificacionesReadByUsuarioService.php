<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion;

use App\Usuarios\Application\Dto\Notificacion\NotificacionUsuarioDto;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionesReadByUsuarioService
{

    public function __construct(private NotificacionRepositoryInterface $notificacionRepository, private NotificacionUsuarioDto $notificacionDto)
    {
    }

    public function __invoke(Usuario $usuario):JsonResponse
    {
        $notificaciones = $this->notificacionRepository->findByUsuario($usuario);
        if ($notificaciones === []){
            return new JsonResponse(['message' => 'El usuario ' . $usuario->getEmail()->value() . ' no ha recibido ninguna notificacion'], Response::HTTP_OK);
        }
        $notificaciones = $this->notificacionDto->collectionFromEntities($notificaciones);
        return new JsonResponse(['message'=>'Estas son todas las notificaciones recibidas por el usuario con email: '.$usuario->getEmail()->value(),'notificaciones' => $notificaciones], Response::HTTP_OK);
    }

}