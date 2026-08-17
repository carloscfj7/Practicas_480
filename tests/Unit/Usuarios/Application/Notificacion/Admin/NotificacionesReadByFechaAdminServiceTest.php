<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion\Admin;

use App\Shared\Application\Exceptions\InvalidDateException;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadByFechaAdminService;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadByFechaAdminServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadByFechaAdminService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);
        $this->service = new NotificacionesReadByFechaAdminService($this->notificacionRepository, $this->notificacionCreadorDto);
    }



    public function testShouldThrowInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->service->__invoke(['fecha' => 'fecha-incorrecta']);
    }

    public function testShouldNotReadAnyNotificacion(): void
    {
        $this->notificacionRepository
            ->method('findByFecha')
            ->willReturn([]);

        $response = $this->service->__invoke(['fecha' => '2024-04-01']);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('No hay ninguna notificacion creada en la fecha proporionada', $content['message']);
    }

    public function testShouldReadNotificacionesSuccessfully(): void
    {
        $notificaciones = [$this->createMock(Notificacion::class)];

        $this->notificacionRepository
            ->method('findByFecha')
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn([
                ['id' => 1, 'mensaje' => 'Notificación ejemplo']
            ]);

        $response = $this->service->__invoke(['fecha' => '2024-04-01']);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            'Estas son todas las notificaciones recibidas el dia: 2024-04-01',
            $content['message']
        );
        $this->assertCount(1, $content['notificaciones']);
    }
}
