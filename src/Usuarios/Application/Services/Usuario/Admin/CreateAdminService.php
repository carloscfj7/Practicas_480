<?php
declare(strict_types=1);

namespace App\Usuarios\Application\Services\Usuario\Admin;


use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Dto\Usuario\DataResponse\RegistroUsuarioDto;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use App\Usuarios\Domain\Exceptions\Usuario\UsuarioExistenteException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class CreateAdminService
{

    public function __construct(private RegistroUsuarioService $registroService)
    {
    }

    public function __invoke(CredentialsDto $data): RegistroUsuarioDto
    {

        $credentials = new CredentialsDto($data->email, $data->password, ['ROLE_ADMIN']);
        $this->registroService->__invoke($credentials);
        return new RegistroUsuarioDto('Admin creado correctamente', $data->email);
    }

}