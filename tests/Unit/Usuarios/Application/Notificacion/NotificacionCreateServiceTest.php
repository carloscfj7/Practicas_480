<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Notificacion;

use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Services\Notificacion\NotificacionCreateService;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificacionCreateServiceTest extends Unit
{
    private NotificacionRepositoryInterface $notificacionRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private NotificacionCreateService $service;

    protected function setUp(): void
    {
        $this->notificacionRepository = $this->createMock(NotificacionRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->service = new NotificacionCreateService($this->notificacionRepository, $this->usuarioRepository);
    }

    private function createMockUsuario(string $email): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email($email));
        return $usuario;
    }

    public function testShouldThrowException(): void
    {
        $usuario = $this->createMockUsuario('creator@example.com');
        $data = [];

        $this->expectException(RequiredDataException::class);
        $this->service->__invoke($usuario, $data);
    }

    public function testShouldCreateNotificacionesuccessfully(): void
    {
        $usuario = $this->createMockUsuario('creator@example.com');
        $data = [
            'mensaje' => 'Test notification',
            'usuarios' => ['user1@example.com', 'user2@example.com']
        ];

        $user1 = $this->createMockUsuario('user1@example.com');
        $user2 = $this->createMockUsuario('user2@example.com');

        $this->usuarioRepository
            ->expects($this->exactly(2))
            ->method('validateUsuario')
            ->with($this->isType('string'))
            ->willReturnOnConsecutiveCalls($user1, $user2);

        $this->notificacionRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Notificacion::class));

        $response = $this->service->__invoke($usuario, $data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Notificacion creada correctamente', $content['message']);
    }


}
