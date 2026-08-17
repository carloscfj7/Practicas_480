<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Usuario;

use App\Usuarios\Application\Services\Usuario\UsuarioDeleteService;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class UsuarioDeleteServiceTest extends Unit
{
    private UsuarioRepositoryInterface $usuarioRepository;
    private UsuarioDeleteService $service;

    protected function setUp(): void
    {
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->service = new UsuarioDeleteService($this->usuarioRepository);
    }

    public function testShouldDeleteUsuarioSuccessfully(): void
    {
        $usuarioMock = $this->createMock(Usuario::class);

        $this->usuarioRepository
            ->expects($this->once())
            ->method('remove')
            ->with($usuarioMock);

        $response = $this->service->__invoke($usuarioMock);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Usuario eliminado correctamente', $content['message']);
    }
}
