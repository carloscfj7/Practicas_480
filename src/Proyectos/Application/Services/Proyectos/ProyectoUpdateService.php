<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Domain\Exceptions\Proyecto\EmptyConsultoresException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoUpdateService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['nombre']);

        $actualizado = [];

        if (!empty($data['descripcion'])) {
            $this->updateDescripcion($proyecto, $data['descripcion'], $actualizado);
        }

        if (!empty($data['fecha_fin'])) {
            $fechaResponse = $this->updateFechaFin($proyecto, $data['fecha_fin'], $actualizado);
            if ($fechaResponse instanceof \Symfony\Component\HttpFoundation\JsonResponse) {
                return $fechaResponse;
            }
        }

        if (!empty($data['estado'])) {
            $this->updateEstado($proyecto, $data['estado'], $actualizado);
        }
        $added = [];
        if (!empty($data['añadir_consultores'])) {
            $added = $this->addConsultores($data['añadir_consultores'], $proyecto, $actualizado);
        }

        if (!empty($data['borrar_consultores'])) {
            $this->removeConsultores($data['borrar_consultores'], $proyecto, $actualizado,$added);
        }

        if (empty($actualizado)) {
            return new JsonResponse(["error" => "No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales", 'status' => Response::HTTP_BAD_REQUEST]);
        }

        $this->proyectoRepository->save($proyecto);

        return new JsonResponse(["message" => "Datos actualizados correctamente", "actualizacion" => $actualizado], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new RequiredDataException();
        }
    }


    private function validateFechaFin($fecha_fin, \DateTimeInterface $fecha_ini): ?JsonResponse
    {
        $fecha_fin = \DateTime::createFromFormat('Y-m-d', $fecha_fin);
        if (!$fecha_fin) {
            throw new InvalidDateException();
        }
        if ($fecha_fin < $fecha_ini) {
            throw new InvalidDateRangeExcpecion();
        }
        return null;
    }


    private function updateDescripcion(Proyecto $proyecto, $descripcion, &$actualizado): void
    {
        if ($descripcion !== $proyecto->getDescripcion()) {
            $proyecto->setDescripcion($descripcion);
            $actualizado['descripcion'] = $descripcion;
        }
    }

    private function updateFechaFin(Proyecto $proyecto, $fecha_fin, &$actualizado): ?JsonResponse
    {
        $validationResponse = $this->validateFechaFin($fecha_fin, $proyecto->getFechaIni());
        if ($validationResponse instanceof \Symfony\Component\HttpFoundation\JsonResponse) {
            return $validationResponse;
        }

        if ($fecha_fin !== $proyecto->getFechaFin()) {
            $proyecto->setFechaFin(new \DateTime($fecha_fin));
            $actualizado['fecha_fin'] = $fecha_fin;
        }
        return null;
    }

    private function updateEstado(Proyecto $proyecto, $estado, &$actualizado): void
    {
        if ($estado !== $proyecto->getEstado()->value) {
            $proyecto->setEstado(Estado::from($estado));
            $actualizado['estado'] = $estado;
        }
    }


    private function addConsultores(array $consultores, Proyecto $proyecto, &$actualizado): array
    {
        $added = [];
        foreach ($consultores as $email_consultor) {

            $consultor = $this->consultorRepository->validateConsultor($email_consultor);
            if (!in_array($consultor,$this->proyectoRepository->getConsultoresFromProyecto($proyecto),true)) {
                $this->proyectoRepository->addConsultorToProyecto($proyecto,$consultor);
                $added[] = $email_consultor;
            }
        }
        if ($added !== []) {
            $actualizado['añadir_consultores'] = $added;
        }
        return $added;
    }

    private function removeConsultores(array $consultores, Proyecto $proyecto, &$actualizado,array $added): array
    {
        $removed = [];
        $consultores_proyecto = $this->proyectoRepository->getConsultoresFromProyecto($proyecto) + $added;
        foreach ($consultores as $email_consultor) {
            $consultor = $this->consultorRepository->validateConsultor($email_consultor);

            if (in_array($consultor,$consultores_proyecto,true)) {
                if (abs(count($consultores_proyecto) - count($removed)) == 1) {
                    throw new EmptyConsultoresException();
                }
                $this->proyectoRepository->removeConsultorFromProyectoByEmail($proyecto,$consultor);
                $removed[] = $email_consultor;
            }
        }
        if ($removed !== []) {
            $actualizado['borrar_consultores'] = $removed;
        }
        return $removed;
    }

}