<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Application\Dto\Entity\HabilidadDto;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadReadAllService
{

    public function __construct(private HabilidadRepositoryInterface $habilidadRepository, private HabilidadDto $habilidadDto)
    {
    }

    public function __invoke():JsonResponse
    {
        $habilidades = $this->habilidadRepository->getAll();
        if ($habilidades === []){
            return new JsonResponse(['message' => 'No se encontraron habilidades'], Response::HTTP_OK);
        }
        $habilidades = $this->habilidadDto->collectionFromEntities($habilidades);
        return new JsonResponse(['message' => 'Estas son todas las habilidades disponibles: ','habilidades' => $habilidades], Response::HTTP_OK);
    }

}