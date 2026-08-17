<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Request\Disponibilidad;

class DisponibilidadCreateConsultorResponseDto
{
    public function __construct(public ?string $message = null, public ?string $email = null)
    {
    }
}