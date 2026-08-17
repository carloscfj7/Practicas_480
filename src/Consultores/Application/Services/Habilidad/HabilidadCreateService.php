<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Domain\Exceptions\Habilidad\ExistentHabilidadException;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Nivel;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadCreateService
{
    public function __construct(private HabilidadRepositoryInterface $habilidadRepository)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);
        $checkHabilidad = $this->createHabilidad($data);
        if ($checkHabilidad instanceof \Symfony\Component\HttpFoundation\JsonResponse){
            return $checkHabilidad;
        }
        return new JsonResponse(['message' => 'Habilidad creada correctamente'], Response::HTTP_CREATED);
    }

    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['habilidad']) || empty($data['nivel'])) {
            throw new RequiredDataException();
        }
        return null;
    }

    private function createHabilidad(array $data): ?JsonResponse
    {
        $habilidad = new Habilidad();
        $habilidad->setNombre($data['habilidad']);
        $habilidad->setNivel(Nivel::fromString($data['nivel']));
        $this->checkHabilidad($data);
        $this->habilidadRepository->save($habilidad);
        return null;
    }

    private function checkHabilidad(array $data): ?JsonResponse
    {
        $habilidad = $this->habilidadRepository->findByNombreAndNivel($data['habilidad'], $data['nivel']);
        if ($habilidad instanceof \App\Consultores\Domain\Habilidad) {
            throw new ExistentHabilidadException();
        }
        return null;
    }
}