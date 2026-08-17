<?php

namespace App\Consultores\Application\Services\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorCreateRequestDto;
use App\Consultores\Application\Dto\Response\Consultor\ConsultorCreateResponseDto;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use App\Usuarios\Domain\UsuarioRepositoryInterface;


class ConsultorCreateService
{
    public function __construct(private UsuarioRepositoryInterface   $usuarioRepository,
                                private ConsultorRepositoryInterface $consultorRepository,
                                private HabilidadRepositoryInterface $habilidadRepository,
                                private RegistroUsuarioService       $registroService)
    {
    }

    public function __invoke(ConsultorCreateRequestDto $data): ConsultorCreateResponseDto
    {
        $credentials = new CredentialsDto($data->email, $data->password, ['ROLE_CONSULTOR']);
        $this->registroService->__invoke($credentials);

        $usuario = $this->usuarioRepository->validateUsuario($data->email);
        $consultor = new Consultor();
        $consultor->setNombre($data->nombre);
        $consultor->setApellidos($data->apellidos);
        $consultor->setPerfil($data->perfil);
        $consultor->setUsuario($usuario);

        $this->consultorRepository->save($consultor);

        if (!empty($data->habilidades)) {
            $consultor = $this->habilidadRepository->setHabilidades($data->habilidades, $consultor);
        }
        $this->consultorRepository->save($consultor);
        return new ConsultorCreateResponseDto('El consultor se ha creado correctamente',$data->email);
    }


}