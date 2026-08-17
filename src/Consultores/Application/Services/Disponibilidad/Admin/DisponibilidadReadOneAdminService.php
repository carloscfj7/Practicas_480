<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadOneAdminService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository, private DisponibilidadDto $disponibilidadDto)
    {
    }
    public function __invoke(array $data):JsonResponse
    {
        $this->valdiateRequiredData($data);

        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $fecha_ini = $this->validateDate($data['fecha_ini']);
        $disponibilidad = $this->disponibilidadRepository->validateDisponibilidad($consultor, $fecha_ini);
        $disponibilidad = $this->disponibilidadDto->fromEntity($disponibilidad);
        return new JsonResponse(['message' => 'Estos son los datos de la disponibildiad: ', 'disponibilidad' => $disponibilidad], Response::HTTP_OK);
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