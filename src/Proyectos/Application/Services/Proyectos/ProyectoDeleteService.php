<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos;

use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoDeleteService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['nombre']);
        $this->proyectoRepository->remove($proyecto);
        return new JsonResponse(["message" => "Proyecto eliminado correctamente", "nombre"=>$proyecto->getNombre()], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new RequiredDataException();
        }
    }

}