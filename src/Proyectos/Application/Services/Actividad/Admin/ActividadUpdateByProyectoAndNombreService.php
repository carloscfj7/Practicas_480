<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad\Admin;

use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadUpdateByProyectoAndNombreService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);

        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $actividad = $this->actividadRepository->validateActividadByNombreAndProyecto($data['nombre'], $proyecto);
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
        return new JsonResponse(['message' => "Nueva descrpcion de la actividad",'actualizado' => $actualizado['descripcion']], Response::HTTP_OK );
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['proyecto'])){
            throw new RequiredDataException();
        }
    }



}