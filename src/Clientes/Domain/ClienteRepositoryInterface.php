<?php

namespace App\Clientes\Domain;


use App\Clientes\Domain\ValueObjects\ClienteId;

interface ClienteRepositoryInterface
{
    public function save(Cliente $consultor): void;

    public function findById(ClienteId $id): ?Cliente;

    public function remove(Cliente $cliente): void;

    public function findByEmailUsuario(string $email): ?Cliente;

    public function getAll();
    public function validateClienteOrFails(string $email):Cliente;

    public function validateRemoveOrFail(Cliente $cliente);

}