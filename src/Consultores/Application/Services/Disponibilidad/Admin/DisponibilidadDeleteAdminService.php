<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadDeleteAdminService
{

    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }
    public function __invoke(array $data)
    {
        $this->valdiateRequiredData($data);
        $fecha_ini = $this->validateDate($data['fecha_ini']);
        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $disponibilidad = $this->disponibilidadRepository->validateDisponibilidad($consultor, $fecha_ini);

        $this->disponibilidadRepository->remove($disponibilidad);
        return new JsonResponse(['message' => 'Disponibilidad eliminada'], Response::HTTP_OK);
    }

    private function valdiateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['consultor']) || empty($data['fecha_ini'])) {
            throw new RequiredDataException();
        }
        return null;
    }

    private function validateDate(string $fecha):\DateTime{
        $convertedFecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if (!$convertedFecha) {
            throw new InvalidDateTimeException();
        }
        return $convertedFecha;
    }

}