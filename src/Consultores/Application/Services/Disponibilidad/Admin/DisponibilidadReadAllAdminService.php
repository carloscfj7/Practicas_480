<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadAllAdminService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private DisponibilidadDto $disponibilidadDto)
    {
    }

    public function __invoke():JsonResponse
    {
        $disponibilidades = $this->disponibilidadRepository->getAll();
        if ($disponibilidades === []){
            return new JsonResponse(['message' => 'No existen disponibilidades', 'status' => Response::HTTP_OK], Response::HTTP_OK);
        }
        $disponibilidades = $this->disponibilidadDto->collectionFromEntities($disponibilidades);
        return new JsonResponse(['message'=>'Estas son todas las disponibilidades: ','disponibilidad' => $disponibilidades,], Response::HTTP_OK);
    }

}