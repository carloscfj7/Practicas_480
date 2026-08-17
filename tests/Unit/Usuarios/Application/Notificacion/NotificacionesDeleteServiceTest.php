<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion;

use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Application\Services\Notificacion\NotifiacionesDeleteService;
use App\Usuarios\Domain\Exceptions\Notificacion\NotAllowedNotificacionException;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionesDeleteServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private NotifiacionesDeleteService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->service = new NotifiacionesDeleteService($this->notificacionRepository);
    }

    private function createMockUsuario(string $email, array $roles): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email($email));
        $usuario->method('getRoles')->willReturn($roles);
        return $usuario;
    }

    private function createMockNotificacion(Usuario $creador): Notificacion
    {
        $notificacion = $this->createMock(Notificacion::class);
        $notificacion->method('getCreador')->willReturn($creador);
        return $notificacion;
    }

    public function testShouldThrowNotAllowedNotificacionExceptionn(): void
    {
        $usuario = $this->createMockUsuario('user@example.com', ['ROLE_USER']);
        $creador = $this->createMockUsuario('creator@example.com', ['ROLE_USER']);
        $notificacion = $this->createMockNotificacion($creador);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('validateNotificacicon')
            ->willReturn($notificacion);

        $this->expectException(NotAllowedNotificacionException::class);

        $this->service->__invoke($usuario, new NotificacionId('01BX5ZZKBKACTAV9WEVGEMMVRZ'));
    }

    public function testShouldDeleteNotificacionSuccessfully(): void
    {
        $creador = $this->createMockUsuario('creator@example.com', ['ROLE_USER']);
        $notificacion = $this->createMockNotificacion($creador);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('validateNotificacicon')
            ->willReturn($notificacion);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('delete')
            ->with($notificacion);

        $response = $this->service->__invoke($creador, new NotificacionId('01BX5ZZKBKACTAV9WEVGEMMVRZ'));
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Notificacion eliminada correctamente', $content['message']);
    }

    public function testShouldDeleteNotificacionSuccessfullyWhenUserIsAdmin(): void
    {
        $usuario = $this->createMockUsuario('admin@example.com', ['ROLE_ADMIN']);
        $creador = $this->createMockUsuario('creator@example.com', ['ROLE_USER']);
        $notificacion = $this->createMockNotificacion($creador);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('validateNotificacicon')
            ->willReturn($notificacion);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('delete')
            ->with($notificacion);

        $response = $this->service->__invoke($usuario, new NotificacionId('01BX5ZZKBKACTAV9WEVGEMMVRZ'));
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Notificacion eliminada correctamente', $content['message']);
    }
}
