<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Dto;

use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\ValueObjects\TareaId;

class TareaSimpleDto
{
    public TareaId $id;
    public string $nombre;
    public string $proyecto;
    public  function fromEntity(Tarea $tarea): self
    {
        $dto = new self();
        $dto->id = $tarea->getId() ?? 0;
        $dto->nombre = $tarea->getNombre() ?? '';

        $proyectoEntity = $tarea->getProyecto();
        $dto->proyecto = $proyectoEntity->getNombre() ?? 0;

        return $dto;
    }
}