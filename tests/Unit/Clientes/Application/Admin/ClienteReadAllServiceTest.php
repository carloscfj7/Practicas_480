<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application\Admin;

use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Application\Services\Admin\ClienteReadAllService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ClienteReadAllServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;
    private ClienteDto $clienteDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->clienteDto = $this->createMock(ClienteDto::class);
    }

    public function testShouldReturnClientesSuccessfully(): void
    {
        $cliente = $this->createMock(Cliente::class);
        $cliente2 = $this->createMock(Cliente::class);
        $clientes = [$cliente, $cliente2];

        $this->clienteRepository
            ->method('getAll')
            ->willReturn($clientes);

        $clientesFormateados = [
            ['nombre' => 'Cliente 1', 'contacto' => 'Contacto', 'direccion' => 'Dirección'],
            ['nombre' => 'Ana', 'contacto' => '654321', 'direccion' => 'Calle 2']
        ];

        $this->clienteDto
            ->method('collectionFromEntities')
            ->with($clientes)
            ->willReturn($clientesFormateados);

        $service = new ClienteReadAllService($this->clienteRepository, $this->clienteDto);

        $response = $service();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Los datos de los clientes son: ', $data['message']);

        $this->assertEquals($clientesFormateados, $data['clientes']);
    }

    public function testShouldReturnEmptyMessageWhenNoClientesFound(): void
    {
        $this->clienteRepository
            ->method('getAll')
            ->willReturn([]);

        $service = new ClienteReadAllService($this->clienteRepository, $this->clienteDto);

        $response = $service();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('No existen clientes', $data['message']);
        $this->assertArrayNotHasKey('clientes', $data);
    }
}
