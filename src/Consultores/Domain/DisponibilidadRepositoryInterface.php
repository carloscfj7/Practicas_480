<?php
declare(strict_types=1);


namespace App\Consultores\Domain;

interface DisponibilidadRepositoryInterface
{

    public function save(Disponibilidad $disponibilidad): void;

    public function findByConsultor(Consultor $consultor): array;

    public function findByConsultorAndInicio(Consultor $consultor, \DateTimeInterface $inicio): ?Disponibilidad;

    public function remove(Disponibilidad $disponibilidad): void;

    public function getALl():array;

    public function validateDisponibilidad(Consultor $consultor, \DateTime $fecha_ini):Disponibilidad;


    public function validateExistentDisponibilidad(Consultor $consultor, \DateTime $fecha_ini): void;



}