<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad\Admin;

use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadByProyectoAndNombreService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ActividadDto $actividadDto)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $actividad = $this->actividadRepository->validateActividadByNombreAndProyecto($data['nombre'],$proyecto);
        $actividad = $this->actividadDto->fromEntity($actividad);
        return new JsonResponse(['message' => 'Estos son los datos de la actividad: ','actividades' => $actividad], Response::HTTP_OK);
    }
    private function validateRequiredData(array $data): void
    {
        if (empty($data['proyecto'])){
            throw new RequiredDataException();
        }
    }

}