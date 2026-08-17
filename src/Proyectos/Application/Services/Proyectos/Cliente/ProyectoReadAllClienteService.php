<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Cliente;

use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadAllClienteService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ClienteRepositoryInterface $clienteRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke(Usuario $usuario):JsonResponse
    {
        $cliente = $this->clienteRepository->validateClienteOrFails($usuario->getEmail()->value());
        $proyectos = $this->proyectoRepository->getProyectosByCliente($cliente);
        if ($proyectos === [])
        {
            return new JsonResponse(['message' => 'El cliente ' . $usuario->getEmail()->value() . ' no tiene ningun proyecto asociado'], Response::HTTP_OK);
        }
        $result = $this->proyectoDto->collectionFromEntities($proyectos);
        return new JsonResponse(['message'=>'Estos son todos los proyectos del cliente con email: '.$usuario->getEmail()->value(),"proyectos" => $result], Response::HTTP_OK);
    }


}