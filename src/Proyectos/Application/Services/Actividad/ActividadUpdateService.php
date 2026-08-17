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

class ActividadUpdateService
{

    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data, Usuario $usuario)
    {
        $this->validateRequiredData($data);

        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndConsultor($data['proyecto'], $consultor);
        $actividad = $this->actividadRepository->validateActividadByNombreProyectoAndUsuario($data['nombre'], $proyecto,$usuario);
        $actualizado = [];

        if (!empty($data['descripcion']) && $data['descripcion'] !== $actividad->getDescripcion()) {
            $actividad->setDescripcion($data['descripcion']);
            $actualizado['descripcion'] = $data['descripcion'];
        }
        if ($actualizado === [])
        {
            return new JsonResponse(['message' => 'No se ha actualizado ninguna informacion'], Response::HTTP_OK);
        }
        $this->actividadRepository->save($actividad);
        return new JsonResponse(['message' => "Nueva descrpcion de la actividad",'actualizado' => $actualizado['descripcion']], Response::HTTP_OK);

    }

    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['proyecto'])){
            throw new RequiredDataException();
        }
    }

}