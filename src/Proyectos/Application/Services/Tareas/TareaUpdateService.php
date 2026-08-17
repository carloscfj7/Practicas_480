<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas;

use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Application\Exceptions\Tarea\TareaNotFoundException;
use App\Proyectos\Domain\Exceptions\Proyecto\EmptyConsultoresException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaUpdateService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private ProyectoRepositoryInterface $proyectoRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $tarea = $this->tareaRepository->validateTareaByProyectoAndNombre($data['nombre'], $proyecto);
        $actualizado = [];

        if (!empty($data['descripcion']) && $data['descripcion'] !== $tarea->getDescripcion()) {
            $tarea->setDescripcion($data['descripcion']);
            $actualizado['descripcion'] = $data['descripcion'];
        }
        if (!empty($data['fecha_fin'])) {
            $fecha_fin = $this->validateDate($data['fecha_fin']);
            $this->validateDates($tarea->getFechaIni(),$fecha_fin );
            if ($fecha_fin !== $tarea->getFechaFin()) {
                $tarea->setFechaFin($fecha_fin);
                $tarea->setEstimacion();
                $actualizado['fecha_fin'] = $data['fecha_fin'];
                $actualizado['estimacion'] = $tarea->getEstimacion();
            }
        }

        if (!empty($data['estado']) && $data['estado'] !== $tarea->getEstado()->value) {
            $tarea->setEstado(Estado::from($data['estado']));
            $actualizado['estado'] = $data['estado'];
        }
        $added = [];
        if (!empty($data['añadir_consultores'])) {
           $added =  $this->addConsultores($data['añadir_consultores'], $tarea, $actualizado);
        }

        if (!empty($data['borrar_consultores'])) {
            $this->removeConsultores($data['borrar_consultores'], $tarea, $actualizado, $added );
        }

        if (empty($actualizado)) {
            return new JsonResponse(["message" => "No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales"], Response::HTTP_OK);
        }

        $this->tareaRepository->save($tarea);



        return new JsonResponse(["message" => "Datos actualizados correctamente", "actualizacion" => $actualizado], Response::HTTP_OK);

    }

    private function validateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['nombre']) || empty($data['proyecto'])) {
            throw new RequiredDataException();
        }
        return null;
    }

    private function validateDate(string $fecha):\DateTime
    {
        $convertedFecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if (!$convertedFecha){
            throw  new InvalidDateTimeException();
        }
        return $convertedFecha;
    }
    private function validateDates(\DateTime $fecha_ini, \DateTime $fecha_fin): ?JsonResponse
    {
        if ($fecha_ini > $fecha_fin) {
            throw new InvalidDateRangeExcpecion();
        }
        return null;
    }

    private function addConsultores(array $consultores, Tarea $tarea, &$actualizado): array
    {
        $added = [];
        foreach ($consultores as $email_consultor) {

            $consultor = $this->consultorRepository->validateConsultor($email_consultor);
            if (!in_array($consultor,$this->tareaRepository->getConsultoresByTarea($tarea),true)) {
                $this->tareaRepository->addConsultorToTarea($tarea,$consultor);
                $added[] = $email_consultor;
            }
        }
        if ($added !== []) {
            $actualizado['añadir_consultores'] = $added;
        }
        return $added;
    }

    private function removeConsultores(array $consultores, Tarea $tarea, &$actualizado, array $added): array
    {
        $removed = [];
        $consultores_tarea = $this->tareaRepository->getConsultoresByTarea($tarea) + $added;
        foreach ($consultores as $email_consultor) {
            $consultor = $this->consultorRepository->validateConsultor($email_consultor);

            if (in_array($consultor,$consultores_tarea,true)) {
                if (abs(count($consultores_tarea) - count($removed)) == 1) {
                    throw new EmptyConsultoresException("No se puede eliminar el consultor ya que es el ultimo consultor de la tarea");
                }
                $this->tareaRepository->removeConsultorFromTareaByEmail($tarea, $consultor);
                $removed[] = $email_consultor;
            }
        }
        if ($removed !== []) {
            $actualizado['borrar_consultores'] = $removed;
        }
        return $removed;
    }


}