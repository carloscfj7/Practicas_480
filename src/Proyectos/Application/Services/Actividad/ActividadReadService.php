<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository, private ActividadDto $activdadDto)
    {
    }

    public function __invoke(array $data, Usuario $usuario): ?JsonResponse
    {
        $this->validateRequiredData($data);
        $consultor  = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndConsultor($data['proyecto'], $consultor);
        $actividad = $this->actividadRepository->validateActividadByNombreProyectoAndUsuario($data['nombre'], $proyecto, $usuario);
        if (!$actividad){
            return new JsonResponse(['message' => 'No existe ninguna actividad con ese nombre en el proyecto proporcionado'],Response::HTTP_OK);
        }
        $actividad = $this->activdadDto->fromEntity($actividad);
        return new JsonResponse(['message' => 'Estos son los datos de la actividad: ', 'actividad' => $actividad],  Response::HTTP_OK);
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['proyecto']))
        {
            throw new RequiredDataException();
        }
    }

}