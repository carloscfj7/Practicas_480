<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion;

use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionCreateService
{

    public function __construct(private NotificacionRepositoryInterface $notificacionRepository, private UsuarioRepositoryInterface $usuarioRepository)
    {
    }

    public function __invoke(Usuario $usuario, array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        return $this->createNotificacion($usuario, $data);
    }

    private function validateRequiredData(array $data):void
    {
        if (empty($data['mensaje']) || empty($data['usuarios'])){
            throw new RequiredDataException();
        }
    }

    private function createNotificacion(Usuario $usuario, array $data):JsonResponse
    {
        $notificacion = new Notificacion();
        $notificacion->setCreador($usuario);
        $notificacion->setMensaje($data['mensaje']);
        $fecha_actual = new \DateTime();
        $notificacion->setFecha($fecha_actual);
        foreach ($data['usuarios'] as $email) {
            $receptor = $this->usuarioRepository->validateUsuario($email);
            $notificacion->addUsuario($receptor);
        }
        $this->notificacionRepository->save($notificacion);
        return new JsonResponse(['message' => 'Notificacion creada correctamente'], Response::HTTP_OK);
    }


}


