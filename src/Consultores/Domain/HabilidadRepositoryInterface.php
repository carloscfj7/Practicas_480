<?php
declare(strict_types=1);


namespace App\Consultores\Domain;

interface HabilidadRepositoryInterface
{
    public function save(Habilidad $habilidad): void;
    public function remove(Habilidad $habilidad): void;

    public function findByNombreAndNivel(string $nombre, string $nivel): ?Habilidad;

    public function getHabilidadesByConsultor(Consultor $consultor): array;


    public function getAll(): array;

    public function validateHabilidad(array $dataHabilidad): Habilidad;

    public function setHabilidades(array $habilidades, Consultor $consultor): Consultor;


    public function valdiateExistentHabilidad(Habilidad $habilidad);


}