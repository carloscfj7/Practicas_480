<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Admin;

use App\Clientes\Domain\Cliente;
use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequieredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['nombre']);
        $result = $this->proyectoDto->fromEntity($proyecto);
        return new JsonResponse(['message'=>'Estos son los datos del proyecto',"proyecto" => $result], Response::HTTP_OK);
    }

    private function validateRequieredData(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new RequiredDataException();
        }
    }

}
