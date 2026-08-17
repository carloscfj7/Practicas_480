<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Notificacion;

use App\Usuarios\Application\Dto\Usuario\Entity\UsuarioDto;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;

class NotificacionCreadorDto
{
    public NotificacionId $id;
    public string $mensaje;
    public string $fecha;
    public string $creador;

    public array $usuarios = [];

    public function fromEntity(Notificacion $notificacion): self
    {
        $dto = new self();
        $dto->id = $notificacion->getId();
        $dto->mensaje = $notificacion->getMensaje();
        $dto->fecha = $notificacion->getFecha() instanceof \DateTimeInterface ? $notificacion->getFecha()->format('Y-m-d H:i:s') : '';

        $creadorEntity = $notificacion->getCreador();
        if ($creadorEntity instanceof Usuario) {
            $dto->creador = $creadorEntity->getEmail()->value();
        }

        $recipientDtos = [];
        foreach ($notificacion->getUsuarios() as $usuarioEntity) {
            if ($usuarioEntity instanceof Usuario) {
                $recipientDtos[] = UsuarioDto::fromEntity($usuarioEntity);
            }
        }
        $dto->usuarios = $recipientDtos;

        return $dto;
    }

    public function collectionFromEntities(array $notificaciones): array
    {
        return array_map(fn(Notificacion $n) => self::fromEntity($n), $notificaciones);
    }
}