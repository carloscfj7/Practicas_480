<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion;

use App\Usuarios\Application\Exceptions\Notificacion\NotificacionNotFoundException;
use App\Usuarios\Domain\Exceptions\Notificacion\NotAllowedNotificacionException;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotifiacionesDeleteService
{

    public function __construct(private NotificacionRepositoryInterface $notificacionRepository)
    {
    }

    public function __invoke(Usuario $usuario, NotificacionId $id)
    {
        $notificacion = $this->notificacionRepository->validateNotificacicon($id);
        $this->checkPermissions($notificacion, $usuario);
        $this->notificacionRepository->delete($notificacion);
        return new JsonResponse(["message" => "Notificacion eliminada correctamente"], Response::HTTP_OK);
    }



    private function checkPermissions(Notificacion $notificacion, Usuario $usuario):void{
        if ($notificacion->getCreador() !== $usuario && !in_array('ROLE_ADMIN', $usuario->getRoles(), true)) {
            throw new NotAllowedNotificacionException();
        }
    }
}