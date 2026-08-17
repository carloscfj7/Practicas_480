<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services\Admin;

use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Domain\ClienteRepositoryInterface;
final readonly class  ClienteReadByEmailService
{

    public function __construct(private ClienteRepositoryInterface $clienteRepository, private ClienteDto $clienteDto)
    {
    }

    public function __invoke(array $data): ClienteDto
    {
        $cliente = $this->clienteRepository->validateClienteOrFails($data['email']);
        return $this->clienteDto->fromEntity($cliente);
    }

}