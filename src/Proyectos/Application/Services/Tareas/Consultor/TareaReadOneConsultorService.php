<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadOneConsultorService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data, Usuario $usuario)
    {
        $this->validateRequiredData($data);
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $tarea = $this->tareaRepository->validateTareaByConsultorAndNombre($data['nombre'],$consultor );
        $tarea = $this->tareaDto->fromEntity($tarea);
        return new JsonResponse(['message'=>'Estos son los datos de la tarea',"tarea" => $tarea],Response::HTTP_OK );
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new RequiredDataException();
        }
    }

}