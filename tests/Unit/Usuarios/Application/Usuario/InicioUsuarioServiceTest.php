<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Usuario;

use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\InicioUsuarioService;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use App\Usuarios\Application\Exceptions\Usuario\InvalidCredentialsException;
use Codeception\Test\Unit;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InicioUsuarioServiceTest extends Unit
{
    private UsuarioRepositoryInterface $usuarioRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private InicioUsuarioService $service;

    protected function setUp(): void
    {
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->service = new InicioUsuarioService($this->usuarioRepository, $this->passwordHasher, $this->jwtManager);
    }

    public function testShouldReturnTokenWhenCredentialsAreValid(): void
    {
        $data = new CredentialsDto(email: 'test@example.com', password: 'securepassword');

        $usuarioMock = $this->createMock(Usuario::class);
        $this->usuarioRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($data->email)
            ->willReturn($usuarioMock);

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($usuarioMock, $data->password)
            ->willReturn(true);

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($usuarioMock)
            ->willReturn('jwt-token');

        $response = $this->service->__invoke($data);

        $this->assertEquals('Este es el token de inicio de sesion', $response->message);
        $this->assertEquals('jwt-token', $response->token);
    }

    public function testShouldThrowExceptionWhenCredentialsAreInvalid(): void
    {
        $data = new CredentialsDto(email: 'testexample.com', password: 'securepassword');

        $this->usuarioRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($data->email)
            ->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->__invoke($data);
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid(): void
    {
        $data = new CredentialsDto(email: 'test@example.com', password: 'secur');


        $usuarioMock = $this->createMock(Usuario::class);
        $this->usuarioRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($data->email)
            ->willReturn($usuarioMock);

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($usuarioMock, $data->password)
            ->willReturn(false);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->__invoke($data);
    }
}
