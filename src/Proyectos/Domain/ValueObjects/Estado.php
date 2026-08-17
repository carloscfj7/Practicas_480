<?php
declare(strict_types=1);

namespace App\Proyectos\Domain\ValueObjects;
enum Estado:string
{

    case PENDIENTE = 'pendiente';
    case EN_PROCESO = 'en proceso';
    case COMPLETADO = 'completado';

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_PROCESO => 'En proceso',
            self::COMPLETADO => 'Completado'
        };
    }
}