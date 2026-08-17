<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Nivel;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadUpdateService
{
    public function __construct(private HabilidadRepositoryInterface $habilidadRepository)
    {
    }

    public function __invoke(array $data): ?JsonResponse
    {
        $this->validateRequiredData($data);
        $habilidad = $this->habilidadRepository->validateHabilidad($data);

        $actualizado = [];

        if (!empty($data['nuevo_nombre']) && $data['nuevo_nombre'] !== $habilidad->getNombre()) {
            $habilidad->setNombre($data['nuevo_nombre']);
            $actualizado['nombre'] = $data['nuevo_nombre'];
        }

        if (!empty($data['nuevo_nivel']) && $data['nuevo_nivel'] !== $habilidad->getNombre()) {
            $habilidad->setNivel(Nivel::fromString($data['nuevo_nivel']));
            $actualizado['nivel'] = $data['nuevo_nivel'];
        }
        if ($actualizado !== []) {
            $this->habilidadRepository->valdiateExistentHabilidad($habilidad);
        }

        if ($actualizado === []){
            return new JsonResponse(['message' => 'No se ha actualizado ningun dato', 'status' => Response::HTTP_OK], Response::HTTP_OK);
        }

        $this->habilidadRepository->save($habilidad);
        return new JsonResponse(['message'=> 'Habilidad actualizada correctamente', 'actualizacion' => $actualizado], Response::HTTP_OK);
    }

    private
    function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['nombre']) || empty($data['nivel'])) {
            throw new RequiredDataException();
        }
        return null;
    }
}