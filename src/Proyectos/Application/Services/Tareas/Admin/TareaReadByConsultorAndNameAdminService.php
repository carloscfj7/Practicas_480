<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByConsultorAndNameAdminService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data)
    {

       $this->validateRequiredFields($data);

        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);

        $tarea = $this->tareaRepository->validateTareaByConsultorAndNombre($data['nombre'], $consultor);
        $tarea = $this->tareaDto->fromEntity($tarea);
        return new JsonResponse(['message'=>'Estos son los datos de la tarea',"tarea" => $tarea], Response::HTTP_OK);
    }


    private function validateRequiredFields(array $data): ?JsonResponse
    {
        if (empty($data['nombre']) || empty($data['consultor'])) {
            throw new RequiredDataException();
        }
        return null;
    }


}