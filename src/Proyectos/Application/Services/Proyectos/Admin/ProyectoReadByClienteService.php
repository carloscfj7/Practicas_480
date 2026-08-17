<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Admin;

use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadByClienteService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ClienteRepositoryInterface $clienteRepository, private ProyectoDto $proyectoDto)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequieredData($data);

        $cliente = $this->clienteRepository->validateClienteOrFails($data['email']);
        $proyectos = $this->proyectoRepository->getProyectosByCliente($cliente);
        if ($proyectos === []){
            return new JsonResponse(['message' => 'El cliente ' . $data['email'] . ' no tiene ningun proyecto asociado'], Response::HTTP_OK);
        }
        $proyectos = $this->proyectoDto->collectionFromEntities($proyectos);

        return new JsonResponse(['message'=>'Estos todos los proyectos del consultor con email: '.$data['email'], "proyectos" => $proyectos], Response::HTTP_OK);

    }

    private function validateRequieredData(array $data): void
    {
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }


}