<?php
declare(strict_types=1);

namespace App\Proyectos\Domain;



use App\Clientes\Domain\Cliente;
use App\Consultores\Domain\Consultor;
use App\Proyectos\Domain\ValueObjects\ProyectoId;

interface ProyectoRepositoryInterface
{
    public function save(Proyecto $proyecto): void;

    public function findById(ProyectoId $id): ?Proyecto;

    public function findByNombre(string $nombre): ?Proyecto;


    public function getProyectosByCliente(Cliente $cliente): array;

    public function getProyectosByConsultor(Consultor $consultor): array;
    public function remove(Proyecto $proyecto): void;

    public function getAll():array;

    public function getProyectoByClienteAndNombre(Cliente $cliente, string $nombre): ?Proyecto;

    public function getProyectoByConsultorAndNombre(Consultor $consultor, string $nombre): ?Proyecto;

    public function removeConsultorFromProyectoByEmail(Proyecto $proyecto, Consultor $consultor): void;

    public function getConsultoresFromProyecto(Proyecto $proyecto): array;

    public function addConsultorToProyecto(Proyecto $proyecto, Consultor $consultor): void;

    public function validateProyectoByNombreAndConsultor(string $nombre, Consultor $consultor):Proyecto;

    public function validateProyectoByNombreAndCliente(string $nombre, Cliente $cliente):Proyecto;


    public function validateProyectoByNombre(string $nombre):Proyecto;

    public function validateExistentProyecto(string $nombre): void;



}