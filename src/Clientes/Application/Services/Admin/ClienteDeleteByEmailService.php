<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services\Admin;

use App\Clientes\Application\Exceptions\ClienteNotFoundException;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Exceptions\Usuario\UsuarioNotFoundException;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClienteDeleteByEmailService
{
    public function __construct(private ClienteRepositoryInterface $clienteRepository)
    {
    }


    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $cliente = $this->clienteRepository->validateClienteOrFails($data['email']);
        $this->clienteRepository->validateRemoveOrFail($cliente);
        $this->clienteRepository->remove($cliente);

        return new JsonResponse(['message' => 'Cliente eliminado correctamente'], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data) {
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }


}