<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadAllConsultorService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(Usuario $usuario)
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());

        $tareas = $this->tareaRepository->getTareasByConsultor($consultor);
        if ($tareas === []) {
            return new JsonResponse(["message" => "El consultor " . $usuario->getEmail()->value() . " no tiene ninguna tarea asociada"], Response::HTTP_OK);
        }
        $tareas = $this->tareaDto->collectionFromEntities($tareas);
        return new JsonResponse(['message'=>'Estas son todas las tareas',"tareas" => $tareas, 'status' => Response::HTTP_OK]);
    }

}