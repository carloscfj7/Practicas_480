<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Usuario;

use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use App\Usuarios\Domain\Exceptions\Usuario\InvalidPasswordException;
use App\Usuarios\Domain\Exceptions\Usuario\UsuarioExistenteException;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistroUsuarioServiceTest extends Unit
{
    private UsuarioRepositoryInterface $usuarioRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private RegistroUsuarioService $service;

    protected function setUp(): void
    {
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->service = new RegistroUsuarioService($this->usuarioRepository, $this->passwordHasher);
    }

    public function testShouldRegisterUserSuccessfully(): void
    {
        $data = new CredentialsDto(email: 'test@example.com',
            password: 'securepassword',
            roles: ['ROLE_ADMIN']);

        $this->usuarioRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($data->email)
            ->willReturn(null);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(Usuario::class), $data->password)
            ->willReturn('hashedpassword');

        $this->usuarioRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Usuario::class));

        $response = $this->service->__invoke($data);

        $this->assertEquals('Usuario creado correctamente', $response->message);
        $this->assertEquals('test@example.com', $response->email);
    }

    public function testShouldThrowExceptionWhenPasswordIsTooShort(): void
    {
        $data = new CredentialsDto(email: 'test@example.com',
            password: 'secu');


        $this->expectException(InvalidPasswordException::class);

        $this->service->__invoke($data);
    }

    public function testShouldThrowExceptionWhenEmailAlreadyExists(): void
    {
        $data = new CredentialsDto(email: 'test@example.com',
            password: 'securepassword',
            roles: ['ROLE_ADMIN']);


        $existingUserMock = $this->createMock(Usuario::class);
        $this->usuarioRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($data->email)
            ->willReturn($existingUserMock);

        $this->expectException(UsuarioExistenteException::class);

        $this->service->__invoke($data);
    }
}
