<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion\Admin;

use App\Shared\Application\Exceptions\InvalidDateException;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadByFechaUsuarioAdminService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadByFechaUsuarioAdminServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadByFechaUsuarioAdminService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);
        $this->service = new NotificacionesReadByFechaUsuarioAdminService($this->notificacionRepository, $this->usuarioRepository, $this->notificacionCreadorDto);
    }

    private function createMockUsuario(): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('admin@example.com'));
        return $usuario;
    }



    public function testShouldThrowInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);
        $usuario = $this->createMockUsuario();

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->service->__invoke([
            'fecha' => 'invalid-date-format',
            'email' => 'admin@example.com'
        ]);
    }

    public function testShouldNotReadAnyNotificacion(): void
    {
        $usuario = $this->createMockUsuario();

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByFechaYUsuario')
            ->willReturn([]);

        $response = $this->service->__invoke([
            'fecha' => '2024-04-01',
            'email' => 'admin@example.com'
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            'No hay ninguna notificacion recibida en la fecha proporcionada por el usuario admin@example.com',
            $content['message']
        );
    }

    public function testShouldReadNotificacionesSuccessfully(): void
    {
        $usuario = $this->createMockUsuario();
        $notificaciones = [$this->createMock(Notificacion::class)];

        $formatted = [
            ['id' => 1, 'message' => 'Notificación de prueba']
        ];

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByFechaYUsuario')
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn($formatted);

        $response = $this->service->__invoke([
            'fecha' => '2024-04-01',
            'email' => 'admin@example.com'
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            'Estas son todas las notificaciones recibidas por el usuario con email: admin@example.comel dia: 2024-04-01',
            $content['message']
        );
        $this->assertCount(1, $content['notificaciones']);
    }
}
