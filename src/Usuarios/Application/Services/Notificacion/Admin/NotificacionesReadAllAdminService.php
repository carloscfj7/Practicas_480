<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion\Admin;

use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionesReadAllAdminService
{
    public function __construct(private NotificacionRepositoryInterface $notificacionRepository, private NotificacionCreadorDto $notificacionCreadorDto)
    {
    }

    public function __invoke():JsonResponse
    {
        $notificaciones = $this->notificacionRepository->getAll();
        if ($notificaciones === [])
        {
            return new JsonResponse(['message' => 'No hay ninguna notificacion'], Response::HTTP_OK);
        }
        $notificaciones = $this->notificacionCreadorDto->collectionFromEntities($notificaciones);
        return new JsonResponse(['message'=>'Estas son todas las notificaciones','notificaciones' => $notificaciones], Response::HTTP_OK);
    }

}