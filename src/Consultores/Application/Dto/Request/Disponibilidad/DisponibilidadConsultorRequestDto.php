<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Request\Disponibilidad;

class DisponibilidadConsultorRequestDto
{
    public function __construct(public ?string $fecha_ini = null,
                                public ?string $fecha_fin = null,
                                public ?bool   $disponible = null)
    {
    }
}