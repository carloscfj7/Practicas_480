<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadDeleteService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data, Usuario $usuario): JsonResponse
    {
        $this->validateRequiredData($data);
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndConsultor($data['proyecto'], $consultor);
        $actividad = $this->actividadRepository->validateActividadByNombreProyectoAndUsuario($data['nombre'], $proyecto, $usuario);
        $this->actividadRepository->remove($actividad);
        return new JsonResponse(['message' => 'Actividad eliminada correctamente'], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data):void
    {
        if (empty($data['nombre']) || empty($data['proyecto']))
        {
            throw new RequiredDataException();
        }
    }
}