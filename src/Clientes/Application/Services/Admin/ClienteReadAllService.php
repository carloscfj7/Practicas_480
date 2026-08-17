<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services\Admin;

use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Domain\ClienteRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ClienteReadAllService
{


    public function __construct(private ClienteRepositoryInterface $clienteRepository, private ClienteDto $clienteDto)
    {
    }

    public function __invoke(): JsonResponse
    {
        $clientes = $this->clienteRepository->getAll();

        if (!$clientes) {
            return new JsonResponse(['message' => 'No existen clientes'], Response::HTTP_OK);
        }

        $clientes = $this->clienteDto->collectionFromEntities($clientes);

        return new JsonResponse(['message' => 'Los datos de los clientes son: ', 'clientes' => $clientes], Response::HTTP_OK);
    }
}