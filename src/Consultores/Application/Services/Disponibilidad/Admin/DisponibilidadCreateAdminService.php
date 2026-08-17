<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadCreateAdminService
{

    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);
        $this->createDisponibilidad($data);
        return new JsonResponse(['message' => 'Disponibilidad creada correctamente para el consultor con email: ' . $data['consultor']], Response::HTTP_CREATED);
    }

    private function createDisponibilidad(array $data)
    {
        $disponibilidad = new Disponibilidad();
        $disponibilidad->setDisponible($data['disponible']);
        $fecha_ini = $this->validateDate($data['fecha_ini']);
        $fecha_fin = $this->validateDate($data['fecha_fin']);
        $this->validateDates($fecha_ini, $fecha_fin);
        $disponibilidad->setFechaIni($fecha_ini);
        $disponibilidad->setFechaFin($fecha_fin);
        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $disponibilidad->setConsultor($consultor);
        $disponibilidad->setConsultor($consultor);
        $this->disponibilidadRepository->validateExistentDisponibilidad($consultor, $fecha_ini);
        $this->disponibilidadRepository->save($disponibilidad);
    }


    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['fecha_ini']) || empty($data['consultor'])) {
            throw new RequiredDataException();
        }
        return null;
    }


    private function validateDate(string $fecha): \DateTime
    {
        $convertedFecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if (!$convertedFecha) {
            throw new InvalidDateTimeException();
        }
        return $convertedFecha;
    }

    private function validateDates(\DateTimeInterface $fecha_ini, \DateTimeInterface $fecha_fin): ?JsonResponse
    {
        if ($fecha_fin <= $fecha_ini) {
            throw new InvalidDateRangeExcpecion();
        }
        return null;
    }
}