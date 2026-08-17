<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadDeleteService
{
    public function __construct(private HabilidadRepositoryInterface $habilidadRepository)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $habilidad = $this->habilidadRepository->validateHabilidad($data);
        $this->habilidadRepository->remove($habilidad);
        return new JsonResponse(['message' => 'Habilidad eliminada correctamente'], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['nombre'])  || empty($data['nivel'])) {
            throw new RequiredDataException();
        }
        return null;
    }
}