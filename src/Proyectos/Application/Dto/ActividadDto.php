<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Dto;

use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ValueObjects\ActividadId;
use App\Usuarios\Domain\Usuario;

class ActividadDto
{
    public ActividadId $id;
    public string $nombre;
    public string $descripcion;
    public string $fecha;
    public string $usuario;
    public string $proyecto;

    public  function fromEntity(Actividad $actividad): self
    {
        $dto = new self();
        $dto->id = $actividad->getId() ?? 0;
        $dto->nombre = $actividad->getNombre() ?? '';
        $dto->descripcion = $actividad->getDescripcion() ?? '';


        $fecha = $actividad->getFecha() instanceof \DateTimeInterface ? $actividad->getFecha()->format('Y-m-d') : '';

        $dto->fecha = $fecha;
        $proyectoEntity = $actividad->getProyecto();
        $dto->proyecto = $proyectoEntity->getNombre() ?? " ";

        $usuarioEntity = $actividad->getUsuario();
        $dto->usuario = $usuarioEntity->getEmail()->value() ?? " ";

        return $dto;
    }

    public  function collectionFromEntities(array $actividades): array
    {
        return array_map(fn(Actividad $c) => self::fromEntity($c), $actividades);
    }
}