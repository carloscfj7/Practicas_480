<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Application\Dto\Entity\HabilidadDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadReadFromUserService
{
    public function __construct(private HabilidadRepositoryInterface $habilidadRepository, private ConsultorRepositoryInterface $consultorRepository, private HabilidadDto $habilidadDto)
    {
    }

    public function __invoke(Usuario $usuario):JsonResponse
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $habilidades = $this->habilidadRepository->getHabilidadesByConsultor($consultor);
        if ($habilidades === []){
            return new JsonResponse(['message' => 'El consultor con email: '.$usuario->getEmail()->value() . ' no tiene habilidades'], Response::HTTP_OK);
        }
        $habilidades = $this->habilidadDto->collectionFromEntities($habilidades);
        return new JsonResponse(['message' => 'Estas son las habilidades del consultor con email: '.$usuario->getEmail()->value() ,"habilidades" => $habilidades], Response::HTTP_OK);
    }


}