<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion;

use App\Shared\Application\Exceptions\InvalidDateException;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Services\Notificacion\NotificacionesReadByFechaCreadorService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadByFechaCreadorServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadByFechaCreadorService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);
        $this->service = new NotificacionesReadByFechaCreadorService($this->notificacionRepository, $this->notificacionCreadorDto);
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

    public function testShouldThrowInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);

        $usuario = $this->createMockUsuario();
        $data = ['fecha' => 'invalid-date'];

        $this->service->__invoke($usuario, $data);
    }

    public function testShouldNotReadAnyNotificacion(): void
    {
        $usuario = $this->createMockUsuario();

        $this->notificacionRepository
            ->expects($this->once())
            ->method('findByFechaYCreador')
            ->with($this->anything(), $usuario)
            ->willReturn([]);

        $data = ['fecha' => '2025-04-24'];

        $response = $this->service->__invoke($usuario, $data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('No hay ninguna notificacion creada en la fecha proporionada por el usuario user@example.com', $content['message']);
    }

    public function testShouldReadNotificacionesByDateSuccessfully(): void
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
            ->method('findByFechaYCreador')
            ->with($this->anything(), $usuario)
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn($formattedNotificaciones);

        $data = ['fecha' => '2025-04-24'];

        $response = $this->service->__invoke($usuario, $data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las notificaciones creadas por el usuario con email: user@example.com el dia: 2025-04-24', $content['message']);
        $this->assertCount(2, $content['notificaciones']);
        $this->assertEquals('Notificación 1', $content['notificaciones'][0]['message']);
    }
}
