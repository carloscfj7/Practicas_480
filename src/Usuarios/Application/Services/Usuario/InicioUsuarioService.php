<?php
declare(strict_types=1);
namespace App\Usuarios\Application\Services\Usuario;

use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Dto\Usuario\DataResponse\InicioUsuarioDto;
use App\Usuarios\Application\Exceptions\Usuario\InvalidCredentialsException;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InicioUsuarioService
{
    public function __construct(private UsuarioRepositoryInterface $usuarioRepository, private UserPasswordHasherInterface $passwordHasher, private JWTTokenManagerInterface $jwtManager)
    {
    }

    public function __invoke(CredentialsDto $data): InicioUsuarioDto
    {
        $usuario = $this->checkCredentials($data->email, $data->password);
        $token = $this->jwtManager->create($usuario);
        return new InicioUsuarioDto("Este es el token de inicio de sesion", $token);
    }

    private function checkCredentials(string $email, string $password): Usuario{
        $usuario = $this->usuarioRepository->findByEmail($email);
        if (!$usuario || !$this->passwordHasher->isPasswordValid($usuario, $password)) {
            throw new InvalidCredentialsException();
        }
        return $usuario;
    }



}