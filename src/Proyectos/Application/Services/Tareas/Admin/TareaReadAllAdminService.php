<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\TareaRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadAllAdminService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke()
    {
        $tareas = $this->tareaRepository->getAll();
        if ($tareas === []) {
            return new JsonResponse(["message" => "No se encontraron tareas"],Response::HTTP_OK);
        }
        $tareas = $this->tareaDto->collectionFromEntities($tareas);
        return new JsonResponse(['message'=>'Estas son todas las tareas',"tareas" => $tareas], Response::HTTP_OK);
    }
}