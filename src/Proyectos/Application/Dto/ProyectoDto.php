<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Dto;

use App\Clientes\Domain\Cliente;
use App\Consultores\Application\Dto\Entity\ConsultorSimpleDto;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Proyectos\Domain\ValueObjects\ProyectoId;
use App\Usuarios\Domain\Usuario;

class ProyectoDto
{
    public ProyectoId $id;
    public string $nombre;
    public string $descripcion;
    public string $estado;
    public string $fechaInicio;
    public ?string $fechaFin;

    public string $cliente;

    public array $consultores;

    public  function fromEntity(Proyecto $proyecto): self
    {
        $dto = new self;

        $dto->id = $proyecto->getId() ?? 0;
        $dto->nombre = $proyecto->getNombre() ?? '';
        $dto->descripcion = $proyecto->getDescripcion() ?? '';

        $estadoVO = $proyecto->getEstado();
        if ($estadoVO instanceof Estado) {
            $dto->estado = $estadoVO->value;
        }

        $fechaInicioStr = $proyecto->getFechaIni() instanceof \DateTimeInterface ? $proyecto->getFechaIni()->format('Y-m-d') : '';
        $fechaFinStr = $proyecto->getFechaFin() instanceof \DateTimeInterface ? $proyecto->getFechaFin()->format('Y-m-d') : null;

        $dto->fechaInicio = $fechaInicioStr;
        $dto->fechaFin = $fechaFinStr;

        $clienteEntity = $proyecto->getCliente();
        if ($clienteEntity instanceof Cliente) {
            $usuarioCliente = $clienteEntity->getIdUsuario();
            if ($usuarioCliente instanceof Usuario) {
                $dto->cliente = $usuarioCliente->getEmail()->value();
            }
        }

        $consultresDto = new ConsultorSimpleDto();
        $consultores = $proyecto->getConsultores()->toArray();
        if ($consultores) {
            $dto->consultores = $consultresDto->collectionFromEntities($consultores);
        }

        return $dto;
    }

    public  function collectionFromEntities(array $proyectos): array
    {
        return array_map(fn(Proyecto $c) => self::fromEntity($c), $proyectos);
    }
}