<?php

declare(strict_types=1);

namespace App\Clientes\Application\Dto\Entity;

use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ValueObjects\ClienteId;

class ClienteDto
{
    public ClienteId $id;
    public string $nombre;
    public string $direccion;
    public string $contacto;
    public string $usuario;

    public function fromEntity(Cliente $cliente): self
    {
        $dto = new self();
        $dto->id = $cliente->getId();
        $dto->nombre = $cliente->getNombre();
        $dto->direccion = $cliente->getDireccion();
        $dto->contacto = $cliente->getContacto();
        $usuario = $cliente->getIdUsuario();
        $dto->usuario = $usuario->getEmail()->value();

        return $dto;
    }

    public function collectionFromEntities(array $clientes): array
    {
        return array_map(fn(Cliente $c) => self::fromEntity($c), $clientes);
    }
}