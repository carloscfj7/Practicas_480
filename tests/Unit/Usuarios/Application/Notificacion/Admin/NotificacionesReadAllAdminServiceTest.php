<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion\Admin;

use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadAllAdminService;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesReadAllAdminServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private NotificacionCreadorDto $notificacionCreadorDto;
    private NotificacionesReadAllAdminService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->notificacionCreadorDto = $this->createMock(NotificacionCreadorDto::class);

        $this->service = new NotificacionesReadAllAdminService(
            $this->notificacionRepository,
            $this->notificacionCreadorDto
        );
    }

    public function testShouldNotReadAnyNotificacion(): void
    {
        $this->notificacionRepository
            ->method('getAll')
            ->willReturn([]);

        $response = $this->service->__invoke();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('No hay ninguna notificacion', $content['message']);
        $this->assertArrayNotHasKey('notificaciones', $content);
    }

    public function testShouldReturnAllNotificacionesSuccessfully(): void
    {
        $notificaciones = [$this->createMock(Notificacion::class)];

        $this->notificacionRepository
            ->method('getAll')
            ->willReturn($notificaciones);

        $this->notificacionCreadorDto
            ->method('collectionFromEntities')
            ->with($notificaciones)
            ->willReturn([
                ['id' => 1, 'mensaje' => 'Prueba']
            ]);

        $response = $this->service->__invoke();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las notificaciones', $content['message']);
        $this->assertIsArray($content['notificaciones']);
        $this->assertCount(1, $content['notificaciones']);
    }
}
