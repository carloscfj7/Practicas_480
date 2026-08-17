<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Entity;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Usuarios\Domain\Usuario;

class ConsultorSimpleDto
{
    public ConsultorId $id;
    public string $nombre;
    public string $usuario;
    public  function fromEntity(Consultor $consultor): self
    {
        $dto = new self();
        $dto->id = $consultor->getId();
        $dto->nombre = $consultor->getNombre();
        $usuarioEntity = $consultor->getUsuario();
        if ($usuarioEntity instanceof Usuario){
            $dto->usuario = $usuarioEntity->getEmail()->value();
        }
        return $dto;
    }

    public  function collectionFromEntities(array $consultores): array
    {
        return array_map(fn(Consultor $c) => self::fromEntity($c), $consultores);
    }
}