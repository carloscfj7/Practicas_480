<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Dto;

use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ValueObjects\ProyectoId;

class ProyectoSimpleDto
{
    public ProyectoId $id;
    public string $nombre;

    public function fromEntity(Proyecto $proyecto): self
    {
        $dto = new self();
        $dto->id = $proyecto->getId();
        $dto->nombre = $proyecto->getNombre() ?? '';
        return $dto;
    }
}