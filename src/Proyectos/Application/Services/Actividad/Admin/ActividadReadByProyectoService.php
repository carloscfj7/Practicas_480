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

class ActividadReadByProyectoService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ActividadDto $actividadDto)
    {
    }
    public function __invoke(array $data) :JsonResponse
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $actividades = $this->actividadRepository->findByProyecto($proyecto);
        if ($actividades === []){
            return new JsonResponse(['message' => 'El proyecto ' . $proyecto->getNombre() . ' no tiene actividades'], Response::HTTP_OK);
        }
        $actividades = $this->actividadDto->collectionFromEntities($actividades);
        return new JsonResponse(['message'=> 'Estas son la actividades registradas en el proyecto: '.$proyecto->getNombre(), 'actividades' => $actividades], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data):void
    {
        if (empty($data['proyecto'])){
            throw new RequiredDataException();
        }
    }


}