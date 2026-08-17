<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Cliente;

use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneClienteService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ClienteRepositoryInterface $clienteRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke(array $data, Usuario $usuario):JsonResponse
    {
        $this->validateRequieredData($data);
        $cliente = $this->clienteRepository->validateClienteOrFails($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndCliente($data['nombre'], $cliente);
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