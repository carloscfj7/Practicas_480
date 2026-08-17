<?php
declare(strict_types=1);

namespace App\Consultores\Domain;

use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Proyectos\Domain\Proyecto;

interface ConsultorRepositoryInterface
{
    public function save(Consultor $consultor): void;

    public function findById(ConsultorId $id): ?Consultor;

    public function remove(Consultor $consultor): void;

    public function getAll(): array;

    public function findByEmailUsuario(string $email): ?Consultor;

    public function validateConsultor(string $email): Consultor;

    public function addConsultoresToProyecto(Proyecto $proyecto, array $consultores): bool;


}