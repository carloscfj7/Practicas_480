<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByConsultorAdminService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data)
    {

        $this->validateRequiredFields($data);
        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $tareas = $this->tareaRepository->getTareasByConsultor($consultor);
        if ($tareas === []) {
            return new JsonResponse(["message" => "No se encontraron tareas para el consultor " . $consultor->getNombre()], Response::HTTP_OK);
        }
        $tarea = $this->tareaDto->collectionFromEntities($tareas);
        return new JsonResponse(['message'=>'Estas son las tearas del consultor con email: '.$data['consultor'],"tareas" => $tarea], Response::HTTP_OK);
    }


    private function validateRequiredFields(array $data): ?JsonResponse
    {
        if (empty($data['consultor'])) {
            throw new RequiredDataException();
        }
        return null;
    }

}