<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadAllConsultorService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository, private DisponibilidadDto $disponibilidadDto)
    {
    }

    public function __invoke(Usuario $usuario): ?array
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $disponibilidades = $this->disponibilidadRepository->findByConsultor($consultor);
        if ($disponibilidades === []) {
            return null;
        }
        return $this->disponibilidadDto->collectionFromEntities($disponibilidades);

    }

}