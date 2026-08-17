<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas;

use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaDeleteService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ProyectoRepositoryInterface $proyectoRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $tarea = $this->tareaRepository->validateTareaByProyectoAndNombre($data['nombre'], $proyecto);
        $this->tareaRepository->remove($tarea);
        return new JsonResponse(["message" => "Tarea eliminada correctamente" ], Response::HTTP_OK);

    }
    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['nombre']) || empty($data['proyecto'])) {
            throw new RequiredDataException();
        }
        return null;
    }



}