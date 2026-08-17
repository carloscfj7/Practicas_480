<?php

declare(strict_types=1);

namespace App\Proyectos\Application\Services\Actividad;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActividadCreateService
{
    public function __construct(private ActividadRepositoryInterface $actividadRepository, private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data, Usuario $usuario)
    {
        $this->validateRequiredData($data);
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $proyecto = $this->proyectoRepository->validateProyectoByNombreAndConsultor($data['proyecto'], $consultor);
        $fecha = $this->validateDate($data['fecha']);
        $this->createActivity($data, $proyecto, $usuario, $fecha);

        return new JsonResponse(['message' => 'La actividad se ha creado correctamente para el proyecto: ' .$proyecto->getNombre()], Response::HTTP_CREATED);
    }

    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['descripcion']) ||  empty($data['fecha']) || empty($data['proyecto'])) {
            throw new RequiredDataException();
        }
    }

    private function validateDate(string $fecha): \DateTime
    {
        $convertedFecha = \DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$convertedFecha) {
            throw new InvalidDateException();
        }
        return $convertedFecha;
    }



    private function createActivity(array $data, Proyecto $proyecto, Usuario $usuario, \DateTime $fecha):Actividad
    {
        $actividad = new Actividad();
        $actividad->setNombre($data['nombre']);
        $actividad->setDescripcion($data['descripcion']);
        $actividad->setFecha($fecha);
        $actividad->setProyecto($proyecto);
        $actividad->setUsuario($usuario);
        $this->actividadRepository->validateExistentActividad($actividad->getNombre(), $proyecto, $usuario);
        $this->actividadRepository->save($actividad);
        return $actividad;
    }
}