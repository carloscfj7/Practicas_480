<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadUpdateAdminService
{

    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);
        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $fecha_ini = $this->validateDate($data['fecha_ini']);
        $disponibildiad = $this->disponibilidadRepository->validateDisponibilidad($consultor, $fecha_ini);
        $actualizado = [];

        if (array_key_exists('disponible', $data) && $data['disponible'] !== $disponibildiad->isDisponible()) {
            $disponibildiad->setDisponible($data['disponible']);
            $actualizado['disponible'] = $data['disponible'];
        }
        if (!empty($data['fecha_fin']) && $data['fecha_fin'] !== $disponibildiad->getFechaFin()->format('Y-m-d H:i:s')){
            $fecha_fin = $this->validateDate($data['fecha_fin']);
            $this->validateDates($fecha_ini, $fecha_fin);
            $disponibildiad->setFechaFin($fecha_fin);
            $actualizado['fecha_fin'] = $data['fecha_fin'];
        }

        if ($actualizado === []){
            return new JsonResponse(['message' => 'No se ha actualizado ningun dato'], Response::HTTP_OK);
        }

        $this->disponibilidadRepository->save($disponibildiad);
        return new JsonResponse(['message' => 'La disponibilidad se ha actualizado correctamente', 'actualizacion' => $actualizado], Response::HTTP_OK);


    }

    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['fecha_ini']) || empty($data['consultor'])) {
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

    private function validateDates(\DateTimeInterface $fecha_ini, \DateTimeInterface $fecha_fin):?JsonResponse{

        if ($fecha_fin <= $fecha_ini){
            throw new InvalidDateRangeExcpecion();
        }
        return null;
    }


}