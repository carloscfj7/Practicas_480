<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion;

use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Services\Notificacion\NotificacionesReadByCreadorService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadByCreadorServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadByCreadorService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);
        $this->service = new NotificacionesReadByCreadorService($this->notificacionRepository, $this->notificacionCreadorDto);
    }

    private function createMockUsuario()
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('user@example.com'));
        return $usuario;
    }

    private function createMockNotificacion(string $message)
    {
        $notificacion = $this->createMock(Notificacion::class);
        $notificacion->method('getMensaje')->willReturn($message);
        return $notificacion;
    }

    public function testShouldNotReadAnyNotificacion(): void
    {
        $usuario = $this->createMockUsuario();

        $this->notificacionRepository
            ->expects($this->once())
            ->method('findByCreador')
            ->with($usuario)
            ->willReturn([]);

        $response = $this->service->__invoke($usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('El usuario user@example.com no ha creado ninguna notificacion', $content['message']);
    }

    public function testShouldReadNotificacionesByCreatorSuccessfully(): void
    {
        $usuario = $this->createMockUsuario();

        $notificaciones = [
            $this->createMockNotificacion('Notifiacion 1'),
            $this->createMockNotificacion('Notifiacion 2')
        ];

        $formattedNotificaciones = [
            ['message' => 'Notificación 1'],
            ['message' => 'Notificación 2']
        ];

        $this->notificacionRepository
            ->expects($this->once())
            ->method('findByCreador')
            ->with($usuario)
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn($formattedNotificaciones);

        $response = $this->service->__invoke($usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las notificaciones creadas por el usuario con email: user@example.com', $content['message']);
        $this->assertCount(2, $content['notificaciones']);
        $this->assertEquals('Notificación 1', $content['notificaciones'][0]['message']);
    }
}
