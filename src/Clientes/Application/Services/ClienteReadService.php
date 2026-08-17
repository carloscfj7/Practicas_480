<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services;

use App\Clientes\Application\Dto\ClienteDtoFactory;
use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClienteReadService
{
    public function __construct(private ClienteRepositoryInterface $clienteRepository, private ClienteDto $clienteDto)
    {
    }

    public function __invoke(Usuario $usuario): ClienteDto
    {
        $cliente = $this->clienteRepository->findByEmailUsuario($usuario->getEmail()->value());
        return $this->clienteDto->FromEntity($cliente);
    }

}