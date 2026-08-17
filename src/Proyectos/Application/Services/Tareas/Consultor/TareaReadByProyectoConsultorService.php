<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Consultor;


use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoConsultorService
{

    public function __construct(private TareaRepositoryInterface $tareaRepository, private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data, Usuario $usuario)
    {
        $this->validateRequiredData($data);
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());

        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);

        $tareas = $this->tareaRepository->getTareasByProyectoAndConsultor($proyecto, $consultor);
        if ($tareas === null || $tareas === []) {
            return new JsonResponse(["message" => "No se ha encontrado ninguna tarea del consultor " . $usuario->getEmail()->value()  ." en el proyecto " . $proyecto->getNombre()], Response::HTTP_OK);
        }
        $tareas = $this->tareaDto->collectionFromEntities($tareas);
        return new JsonResponse(['message'=>'Estas son todas las tareas del consultor con email '.$usuario->getEmail()->value().' en el proyecto: '.$proyecto->getNombre(),"tareas" => $tareas], Response::HTTP_OK);
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['proyecto'])) {
            throw new RequiredDataException();
        }
    }

}