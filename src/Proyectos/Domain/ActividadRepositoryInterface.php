<?php
declare(strict_types=1);


namespace App\Proyectos\Domain;



use App\Proyectos\Domain\ValueObjects\ActividadId;
use App\Usuarios\Domain\Usuario;

interface ActividadRepositoryInterface
{
    public function save(Actividad $actividad): void;

    public function findById(ActividadId $id): ?Actividad;

    public function findByNombreAndProyecto(string $nombre, Proyecto $proyecto): ?Actividad;
    public function findByNombreProyectoAndUsuario(string $nombre,Proyecto $proyecto, Usuario $usuario): ?Actividad;

    public function findByProyecto(Proyecto $proyecto): array;

    public function findByUsuario(Usuario $usuario): array;
    public function remove(Actividad $actividad): void;

    public function getAll():array;

    public function validateActividadByNombreProyectoAndUsuario(string $nombre, Proyecto $proyecto, Usuario $usuario):Actividad;

    public function validateActividadByNombreAndProyecto(string $nombre, Proyecto $proyecto):Actividad;

    public function validateExistentActividad(string $nombre, Proyecto $proyecto, Usuario $usuario): void;
}