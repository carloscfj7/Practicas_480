<?php
declare(strict_types=1);


namespace App\Usuarios\Domain;


use App\Usuarios\Domain\ValueObjects\NotificacionId;

interface NotificacionRepositoryInterface
{
    public function save(Notificacion $notificacion): void;

    public function delete(Notificacion $notificacion): void;

    public function findByCreador(Usuario $usuario): array;

    public function findByUsuario(Usuario $usuario): array;

    public function findByFecha(\DateTimeInterface $fecha): array;

    public function findById(NotificacionId $id): ?Notificacion;
    public function findByFechaYCreador(\DateTimeInterface $fecha, Usuario $creador): array;

    public function findByFechaYUsuario(\DateTimeInterface $fecha, Usuario $usuario): array;

    public function getAll(): array;

    public function validateNotificacicon(NotificacionId $id):Notificacion;
}