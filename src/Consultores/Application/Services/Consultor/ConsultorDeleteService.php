<?php

namespace App\Consultores\Application\Services\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorDeleteService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(Usuario $usuario): string
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $this->consultorRepository->remove($consultor);

        return 'Consultor eliminado correctamente';
    }

}