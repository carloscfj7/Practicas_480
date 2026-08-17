<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Entity;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\ValueObjects\DisponibilidadId;
use App\Usuarios\Domain\Usuario;

class DisponibilidadDto
{
    public DisponibilidadId $id;
    public bool $disponible = false;
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public string $consultor = '';

    public function fromEntity(Disponibilidad $disponibilidad): self
    {
        $this->id = $disponibilidad->getId();
        $this->disponible = $disponibilidad->isDisponible() ?? false;
        $this->fechaInicio = $disponibilidad->getFechaIni()?->format('Y-m-d H:i:s') ?? '';
        $this->fechaFin = $disponibilidad->getFechaFin()?->format('Y-m-d H:i:s') ?? '';

        $consultorEntity = $disponibilidad->getConsultor();
        if ($consultorEntity instanceof Consultor) {
            $usuarioEntity = $consultorEntity->getUsuario();
            if ($usuarioEntity instanceof Usuario) {
                $this->consultor = $usuarioEntity->getEmail()->value();
            }
        }

        return $this;
    }

    public  function collectionFromEntities(array $disponibilidades): array
    {
        return array_map(function (Disponibilidad $disp) {
            return (new self())->fromEntity($disp);
        }, $disponibilidades);
    }
}
