<?php
declare(strict_types=1);

namespace App\Proyectos\Domain;

use App\Consultores\Domain\Consultor;
use App\Proyectos\Domain\ValueObjects\TareaId;

interface TareaRepositoryInterface
{
    public function save(Tarea $tarea): void;

    public function findById(TareaId $id): ?Tarea;

    public function findByNombre(string $nombre): ?Tarea;

    public function getAll():array;

    public function getTareasByProyecto(Proyecto $proyecto): array;
    public function remove(Tarea $tarea): void;

    public function getTareasByConsultor(Consultor $consultor): array;

    public function getTareasByProyectoAndConsultor(Proyecto $proyecto, Consultor $consultor): ?array;

    public function getTareaByProyectoAndName(Proyecto $proyecto, string $nombre): ?Tarea;

    public function getConsultoresByTarea(Tarea $tarea): array;

    public function getProyectoByTarea(Tarea $tarea): ?Proyecto;

    public function getTareaByConsultorAndName(Consultor $consultor, string $nombre): ?Tarea;

    public function getTareaByConsultorAndProyectoAndName(Consultor $consultor, Proyecto $proyecto, string $nombre): ?Tarea;

    public function getConsultoresFromTarea(Tarea $tarea): array;

    public function addConsultorToTarea(Tarea $tarea, Consultor $consultor): void;
    public function removeConsultorFromTareaByEmail(Tarea $tarea, Consultor $consultor): void;

    public function validateTareaByProyectoAndNombre(string $nombre, Proyecto $proyecto): Tarea;

    public function validateTareaByConsultorAndNombre(string $nombre, Consultor $consultor): Tarea;

    public function validateTareaByConsultorNombreAndProyecto(string $nombre, Consultor $consultor, Proyecto $proyecto): Tarea;


    public function validateExistentTarea(string $nombre, Proyecto $proyecto): void;


}