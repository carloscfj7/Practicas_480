<?php

namespace App\Proyectos\Application\Services\Proyectos\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadAllConsultorService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke(Usuario $usuario):JsonResponse
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyectos = $this->proyectoRepository->getProyectosByConsultor($consultor);
        if ($proyectos === [])
        {
            return new JsonResponse(['message' => 'El consultor ' . $usuario->getEmail()->value() . ' no tiene ningun proyecto asociado'], Response::HTTP_OK);
        }
        $proyecto = $this->proyectoDto->collectionFromEntities($proyectos);
        return new JsonResponse(['message'=>'Estos son todos los proyectos del consultor con email: '.$usuario->getEmail()->value(),"proyectos" => $proyecto], Response::HTTP_OK);
    }



}