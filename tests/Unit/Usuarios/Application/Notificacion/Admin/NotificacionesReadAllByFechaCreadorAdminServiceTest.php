<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion\Admin;

use App\Shared\Application\Exceptions\InvalidDateException;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadAllByFechaCreadorAdminService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadAllByFechaCreadorAdminServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadAllByFechaCreadorAdminService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);

        $this->service = new NotificacionesReadAllByFechaCreadorAdminService(
            $this->notificacionRepository,
            $this->usuarioRepository,
            $this->notificacionCreadorDto
        );
    }



    public function testShouldThrowInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);

        $this->service->__invoke([
            'fecha' => 'fecha-invalida',
            'email' => 'user@example.com'
        ]);
    }

    public function testShouldNotReadAnyNotifiacion(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('admin@example.com'));

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByFechaYCreador')
            ->willReturn([]);

        $response = $this->service->__invoke([
            'email' => 'user@example.com',
            'fecha' => '2024-04-01'
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('No hay ninguna notificacion creada por el usuario', $content['message']);
    }

    public function testShouldReturnNotificacionesSuccessfully(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('admin@example.com'));

        $notificaciones = [$this->createMock(Notificacion::class)];

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByFechaYCreador')
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn([
                ['id' => 1, 'mensaje' => 'Mensaje de prueba']
            ]);

        $response = $this->service->__invoke([
            'email' => 'admin@example.com',
            'fecha' => '2024-04-01'
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las notificaciones creadas por el usuario con email: admin@example.com el dia: 2024-04-01', $content['message']);
        $this->assertCount(1, $content['notificaciones']);
    }
}
