<?php
declare(strict_types=1);
namespace App\Usuarios\Domain;

use App\Shared\Domain\Model\UserId;

interface UsuarioRepositoryInterface
{
    public function save(Usuario $usuario): void;

    public function findByEmail(string $email): ?Usuario;

    public function findById(UserId $id): ?Usuario;

    public function remove(Usuario $usuario): void;

    public function validateUsuario(string $email):Usuario;

}