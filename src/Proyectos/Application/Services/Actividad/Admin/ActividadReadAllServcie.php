<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad\Admin;

use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadAllServcie
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ActividadDto $actividadDto)
    {
    }

    public function __invoke(): JsonResponse
    {
        $actividades = $this->actividadRepository->getAll();
        if ($actividades === []) {
            return new JsonResponse(['message' => 'No existe ninguna actividad'], Response::HTTP_OK);
        }
        $actividades = $this->actividadDto->collectionFromEntities($actividades);
        return new JsonResponse(['message' => 'Estas son todas las actividades: ','actividades' => $actividades], Response::HTTP_OK);
    }
}