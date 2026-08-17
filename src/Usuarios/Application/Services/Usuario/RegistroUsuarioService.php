<?php
declare(strict_types=1);

namespace App\Usuarios\Application;

namespace App\Usuarios\Application\Services\Usuario;

use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Dto\Usuario\DataResponse\RegistroUsuarioDto;
use App\Usuarios\Domain\Exceptions\Usuario\InvalidPasswordException;
use App\Usuarios\Domain\Exceptions\Usuario\UsuarioExistenteException;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistroUsuarioService
{
    public function __construct(private UsuarioRepositoryInterface $usuarioRepository, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(CredentialsDto $data): RegistroUsuarioDto
    {

        $this->checkPassword($data->password);
        $this->checkExistEmail($data->email);

        $roles = empty($data->roles) ? [] : $data->roles;
        $usuario = new Usuario();
        $usuario->setEmail($data->email);
        $usuario->setPassword($this->passwordHasher->hashPassword($usuario, $data->password));

        if (empty($roles)) {
            $roles = ['ROLE_USER'];
        }
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        $usuario->setRoles($roles);

        $this->usuarioRepository->save($usuario);

        return new RegistroUsuarioDto('Usuario creado correctamente', $data->email);

    }

    private function checkPassword(string $password): void
    {
        if (strlen($password) < 6) {
            throw new InvalidPasswordException();
        }

    }

    private function checkExistEmail(string $email): void
    {
        if ($this->usuarioRepository->findByEmail($email) instanceof \App\Usuarios\Domain\Usuario) {
            throw new UsuarioExistenteException();
        }
    }


}