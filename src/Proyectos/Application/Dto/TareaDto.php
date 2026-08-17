<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Dto;

use App\Consultores\Application\Dto\Entity\ConsultorSimpleDto;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Proyectos\Domain\ValueObjects\TareaId;

class TareaDto
{
    public TareaId $id;
    public string $nombre;
    public string $descripcion;
    public string $estado;
    public ?string $estimacion;
    public string $fechaInicio;
    public ?string $fechaFin;
    public ProyectoSimpleDto $proyecto;

    public array $consultores;

    public  function fromEntity(Tarea $tarea): self
    {
        $dto = new self();
        $dto->id = $tarea->getId();
        $dto->nombre = $tarea->getNombre();
        $dto->descripcion = $tarea->getDescripcion();

        $estadoVO = $tarea->getEstado();
        if ($estadoVO instanceof Estado) {
            $dto->estado = $estadoVO->value;
        }

        $fechaInicioStr = $tarea->getFechaIni() instanceof \DateTime ? $tarea->getFechaIni()->format('Y-m-d H:i:s') : '';
        $fechaFinStr = $tarea->getFechaFin() instanceof \DateTime ? $tarea->getFechaFin()->format('Y-m-d H:i:s') : null;

        $dto->fechaInicio = $fechaInicioStr;
        $dto->fechaFin = $fechaFinStr;

        $proyectoDto = new ProyectoSimpleDto();
        $proyectoEntity = $tarea->getProyecto();
        $dto->proyecto = $proyectoDto->fromEntity($proyectoEntity);

        $consultorDto = new ConsultorSimpleDto();
        $consultoresEntity = $tarea->getConsultores()->toArray();
        if (!empty($consultoresEntity)){
            $dto->consultores = $consultorDto->collectionFromEntities($consultoresEntity);
        }

        return $dto;
    }

    public  function collectionFromEntities(array $tareas): array
    {
        return array_map(fn(Tarea $c) => self::fromEntity($c), $tareas);
    }
}