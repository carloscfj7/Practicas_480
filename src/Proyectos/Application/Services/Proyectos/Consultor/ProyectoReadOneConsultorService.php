<?php

namespace App\Proyectos\Application\Services\Proyectos\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneConsultorService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke(array $data, Usuario $usuario):JsonResponse
    {
        $this->validateRequieredData($data);
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndConsultor( $data['nombre'],$consultor);

        $proyecto = $this->proyectoDto->fromEntity($proyecto);
        return new JsonResponse(['message'=>'Estos son los datos del proyecto',"proyecto" => $proyecto], Response::HTTP_OK);
    }

    private function validateRequieredData(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new RequiredDataException();
        }
    }


}