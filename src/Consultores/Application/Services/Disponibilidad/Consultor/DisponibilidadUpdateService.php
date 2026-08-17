<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Dto\Response\UpdateServicesResponseDto;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadUpdateService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(Usuario $usuario, DisponibilidadConsultorRequestDto $data): UpdateServicesResponseDto
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $fecha_ini = $this->validateDate($data->fecha_ini);

        $disponibildiad = $this->disponibilidadRepository->validateDisponibilidad($consultor, $fecha_ini);
        $actualizado = [];


        if (!empty($data->disponible) && ($data->disponible !== $disponibildiad->isDisponible())) {
            $disponibildiad->setDisponible($data->disponible);
            $actualizado['disponible'] = $data->disponible;
        }
        if (!empty($data->fecha_fin) && $data->fecha_fin!== $disponibildiad->getFechaFin()->format('Y-m-d H:i:s')){
            $fecha_fin = $this->validateDate($data->fecha_fin);
            $this->validateDates($fecha_ini, $fecha_fin);
            $disponibildiad->setFechaFin($fecha_fin);
            $actualizado['fecha_fin'] = $data->fecha_fin;
        }

        if ($actualizado === []){
            return new UpdateServicesResponseDto("No se ha actualizado ningun dato");
        }

        $this->disponibilidadRepository->save($disponibildiad);

        return new UpdateServicesResponseDto("La disponibilidad se ha actualizado correctamente", $actualizado);

    }


    private function validateDate(string $fecha):\DateTime{
        $convertedFecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if (!$convertedFecha) {
            throw new InvalidDateTimeException();
        }
        return $convertedFecha;
    }


    private function validateDates(\DateTimeInterface $fecha_ini, \DateTimeInterface $fecha_fin){
        if ($fecha_fin <= $fecha_ini){
            throw new InvalidDateRangeExcpecion();
        }
    }



}