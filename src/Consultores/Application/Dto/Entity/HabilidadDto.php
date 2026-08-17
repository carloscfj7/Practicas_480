<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Entity;

use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\ValueObjects\HabilidadId;

class HabilidadDto
{
    public HabilidadId $id;
    public string $nombre;
    public string $nivel;

    public  function fromEntity(Habilidad $habilidad): self
    {
        $dto = new self();

        $dto->id = $habilidad->getId();
        $dto->nombre = $habilidad->getNombre() ?? '';
        $dto-> nivel = $habilidad->getNivel()->value;


        return $dto;


    }

    public  function collectionFromEntities(array $habilidades): array
    {
        return array_map(fn(Habilidad $c) => self::fromEntity($c), $habilidades);
    }
}