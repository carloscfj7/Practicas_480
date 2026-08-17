<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services;

use App\Clientes\Application\Dto\DataRequest\ClienteCreateRequestDto;
use App\Clientes\Application\Dto\DataResponse\ClienteCreateResponseDto;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;

use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClienteCreateService
{

    public function __construct(private ClienteRepositoryInterface $clienteRepository,
                                private UsuarioRepositoryInterface $usuarioRepository,
                                private RegistroUsuarioService     $registroService)
    {
    }

    public function __invoke(ClienteCreateRequestDto $data): ClienteCreateResponseDto
    {

        $credentials = new CredentialsDto($data->email, $data->password, ['ROLE_CLIENTE']);
        $this->registroService->__invoke($credentials);

        $usuario = $this->usuarioRepository->validateUsuario($data->email);
        $cliente = new Cliente();
        $cliente->setNombre($data->nombre);
        $cliente->setContacto($data->contacto);
        $cliente->setDireccion($data->direccion);
        $cliente->setIdUsuario($usuario);

        $this->clienteRepository->save($cliente);

        return new ClienteCreateResponseDto("Cliente creado correctamente", $data->email);
    }

}
