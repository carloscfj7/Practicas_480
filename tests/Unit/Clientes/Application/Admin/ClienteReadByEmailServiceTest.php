<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application\Admin;

use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Application\Services\Admin\ClienteDeleteByEmailService;
use App\Clientes\Application\Services\Admin\ClienteReadByEmailService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ClienteReadByEmailServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;
    private ClienteDto $clienteDto;

    protected function setUp():void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->clienteDto = $this->createMock(ClienteDto::class);
    }

    public function testShouldReadClienteByEmailSuccessfully():void
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

        $this->clienteDto
            ->method('fromEntity')
            ->with($cliente)
            ->willReturn($this->clienteDto);
        $this->clienteDto->nombre = 'Cliente 1';


        $service = new ClienteReadByEmailService($this->clienteRepository, $this->clienteDto);
        $response = $service($data);

        $this->assertEquals('Cliente 1', $response->nombre);
    }

}