<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application\Admin;

use App\Clientes\Application\Services\Admin\ClienteDeleteByEmailService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ClienteDeleteByEmailServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;

    protected function setUp(): void
    {
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
    }

    public function testShouldDeleteClienteByEmailSuccessfully(): void
    {
        $data = [
            'email' => 'cliente@prueba.es',
        ];

        $cliente = $this->createMock(Cliente::class);

        $this->clienteRepository
            ->expects($this->once())
            ->method('validateClienteOrFails')
            ->with($data['email'])
            ->willReturn($cliente);

        $this->clienteRepository
            ->expects($this->once())
            ->method('remove')
            ->with($cliente);

        $service = new ClienteDeleteByEmailService(
            $this->clienteRepository
        );

        $response = $service($data);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(['message' => 'Cliente eliminado correctamente'], $content);
    }




}