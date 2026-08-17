<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion\Admin;

use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadByEmailCreadorAdminService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadByEmailCreadorAdminServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadByEmailCreadorAdminService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);

        $this->service = new NotificacionesReadByEmailCreadorAdminService(
            $this->notificacionRepository,
            $this->usuarioRepository,
            $this->notificacionCreadorDto
        );
    }



    public function testShouldNotReadAnyNotificacion(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('test@example.com'));
        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByCreador')
            ->willReturn([]);

        $response = $this->service->__invoke(['email' => 'test@example.com']);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('No hay ninguna notificacion creado por el usuario test@example.com', $content['message']);
    }

    public function testShouldReturnNotificacionesSuccessfully(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('test@example.com'));

        $notificaciones = [$this->createMock(Notificacion::class)];

        $this->usuarioRepository
            ->method('validateUsuario')
            ->willReturn($usuario);

        $this->notificacionRepository
            ->method('findByCreador')
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn([
                ['id' => 1, 'mensaje' => 'Notificación de prueba']
            ]);

        $response = $this->service->__invoke(['email' => 'admin@example.com']);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las notificaciones creadas por el usuario con email: test@example.com', $content['message']);
        $this->assertCount(1, $content['notificaciones']);
    }
}
