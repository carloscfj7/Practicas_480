<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadCreateConsultorResponseDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class DisponibilidadCreateService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository,
                                private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(Usuario $usuario,
                             DisponibilidadConsultorRequestDto $data): DisponibilidadCreateConsultorResponseDto
    {
        $this->createDisponibilidad($data, $usuario);
        return new DisponibilidadCreateConsultorResponseDto("Disponibilidad creada correctamente",
            $usuario->getEmail()->value());
    }


    private function createDisponibilidad(DisponibilidadConsultorRequestDto $data, Usuario $usuario){
        $disponibilidad = new Disponibilidad();
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());

        $disponibilidad->setDisponible($data->disponible);

        $fecha_ini = $this->validateDate($data->fecha_ini);
        $fecha_fin = $this->validateDate($data->fecha_fin);
        $this->validateDates($fecha_ini, $fecha_fin);

        $disponibilidad->setFechaIni($fecha_ini);
        $disponibilidad->setFechaFin($fecha_fin);
        $disponibilidad->setConsultor($consultor);

        $this->disponibilidadRepository->validateExistentDisponibilidad($consultor, $fecha_ini);
        $this->disponibilidadRepository->save($disponibilidad);
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