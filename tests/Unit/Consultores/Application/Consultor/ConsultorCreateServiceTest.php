<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorCreateRequestDto;
use App\Consultores\Application\Services\Consultor\ConsultorCreateService;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;


class ConsultorCreateServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private HabilidadRepositoryInterface $habilidadRepository;
    private RegistroUsuarioService $registroService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
        $this->registroService = $this->createMock(RegistroUsuarioService::class);
    }

    public function testCreateConsultorSuccessfully(): void
    {
        $data = new ConsultorCreateRequestDto(email: "consultor14@ejemplo.com",
            password: "password",
            nombre: "Juan",
            apellidos: "Pérez Gómez",
            perfil: Perfil::PROJECT_MANAGER, habilidades: [
            ]);

        $email = new Email($data->email);

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);


        $this->usuarioRepository
            ->expects($this->once())
            ->method('validateUsuario')
            ->with($data->email)
            ->willReturn($usuario);

        $this->consultorRepository
            ->expects($this->any())
            ->method('save');

        $service = new ConsultorCreateService(
            $this->usuarioRepository,
            $this->consultorRepository,
            $this->habilidadRepository,
            $this->registroService
        );

        $result = $service($data);

        $this->assertEquals('El consultor se ha creado correctamente', $result->message);

    }


}