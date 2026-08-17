<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Notificacion;

use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\Usuario;

class NotificacionUsuarioDto
{
    public string $mensaje;
    public string $fecha;
    public string $creador;

    public function fromEntity(Notificacion $notificacion): self
    {
        $dto = new self();
        $dto->mensaje = $notificacion->getMensaje();
        $dto->fecha = $notificacion->getFecha() instanceof \DateTimeInterface ? $notificacion->getFecha()->format('Y-m-d H:i:s') : '';

        $creadorEntity = $notificacion->getCreador();
        if ($creadorEntity instanceof Usuario) {
            $dto->creador = $creadorEntity->getEmail()->value();
        }

        return $dto;
    }

    public function collectionFromEntities(array $notificaciones): array
    {
        return array_map(fn(Notificacion $n) => self::fromEntity($n), $notificaciones);
    }
}