<?php

namespace App\Consultores\Application\Services\Consultor;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository, private ConsultorDto $consultorDto)
    {
    }

    public function __invoke(Usuario $usuario): ConsultorDto
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        return $this->consultorDto->fromEntity($consultor);
    }

}