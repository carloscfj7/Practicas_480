<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services;

use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClienteDeleteService
{

    public function __construct(private ClienteRepositoryInterface $clienteRepository)
    {
    }

    public function __invoke(Usuario $usuario):string
    {
        $cliente = $this->clienteRepository->findByEmailUsuario($usuario->getEmail()->value());
        $this->clienteRepository->validateRemoveOrFail($cliente);
        $this->clienteRepository->remove($cliente);
        return 'Cliente eliminado correctamente';
    }
}