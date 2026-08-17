<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Consultor;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibildiadDeleteService
{
    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }
    public function __invoke(Usuario $usuario, array $data): string
    {

       $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
       $fecha_ini = $this->validateDate($data['fecha_ini']);
       $disponibilidad = $this->disponibilidadRepository->validateDisponibilidad($consultor, $fecha_ini);
       $this->disponibilidadRepository->remove($disponibilidad);

       return 'Disponibilidad eliminada correctamente';
    }




    private function validateDate(string $fecha):\DateTime{
        $convertedFecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if (!$convertedFecha) {
            throw new InvalidDateTimeException();
        }
        return $convertedFecha;
    }

}