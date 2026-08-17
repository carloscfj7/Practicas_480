<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad\Admin;

use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Exceptions\Usuario\UsuarioNotFoundException;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadByUsuarioService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private UsuarioRepositoryInterface $usuarioRepository, private ActividadDto $actividadDto)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        $usuario = $this->usuarioRepository->validateUsuario($data['usuario']);
        $actividades = $this->actividadRepository->findByUsuario($usuario);
        if ($actividades === []){
            return new JsonResponse(['message' => 'El usuario ' . $usuario->getEmail()->value() . ' no tiene ninguna actividad'], Response::HTTP_OK);
        }
        $actividades = $this->actividadDto->collectionFromEntities($actividades);
        return new JsonResponse(['message'=> 'Estas son las actividades del usuario con email: '.$data['usuario'],'actividades' => $actividades, 'status' => Response::HTTP_OK], Response::HTTP_OK);
    }
    private function validateRequiredData(array $data):void
    {
        if (empty($data['usuario'])){
            throw new RequiredDataException();
        }
    }

}