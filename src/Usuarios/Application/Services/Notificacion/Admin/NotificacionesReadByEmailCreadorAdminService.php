<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion\Admin;

use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Exceptions\Usuario\UsuarioNotFoundException;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionesReadByEmailCreadorAdminService
{
    public function __construct(private NotificacionRepositoryInterface $notificacionRepository, private UsuarioRepositoryInterface $usuarioRepository, private NotificacionCreadorDto $notificacionCreadorDto)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $usuario = $this->usuarioRepository->validateUsuario($data['email']);
        $notificaciones = $this->notificacionRepository->findByCreador($usuario);
        if ($notificaciones === [])
        {
            return new JsonResponse(['message' => 'No hay ninguna notificacion creado por el usuario ' . $usuario->getEmail()->value()], Response::HTTP_NOT_FOUND);
        }
        $notificaciones = $this->notificacionCreadorDto->collectionFromEntities($notificaciones);
        return new JsonResponse(['message'=>'Estas son todas las notificaciones creadas por el usuario con email: '.$usuario->getEmail()->value(),'notificaciones' => $notificaciones], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data):void{
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }


}