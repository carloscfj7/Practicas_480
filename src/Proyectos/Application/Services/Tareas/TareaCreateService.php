<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas;

use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Application\Exceptions\Tarea\TareaNotFoundException;
use App\Proyectos\Domain\Exceptions\Tarea\ExistentTareaException;
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

class TareaCreateService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ConsultorRepositoryInterface $consultorRepository, private ProyectoRepositoryInterface $proyectoRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);

        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);
        $this->tareaRepository->validateExistentTarea($data['nombre'], $proyecto);

        $fecha_ini = $this->validateDate($data['fecha_ini']);
        $fecha_fin = $this->validateDate($data['fecha_fin']);
        $this->validateDates($fecha_ini, $fecha_fin);
        $tarea = $this->createTarea($data, $proyecto, $data['consultores']);

        $this->tareaRepository->save($tarea);

        return new JsonResponse(['message' => 'Tarea creada correctamente' , 'nombre' => $tarea->getNombre()], Response::HTTP_CREATED);
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
            throw new InvalidDateTimeException();
        }
        return $convertedFecha;
    }


    private function validateDates(\DateTimeInterface $fecha_ini, \DateTimeInterface $fecha_fin): void
    {
        if ($fecha_ini > $fecha_fin) {
           throw new InvalidDateRangeExcpecion();
        }
    }



    private function createTarea(array $data, Proyecto $proyecto, array $consultores): ?Tarea
    {
        $tarea = new Tarea();
        $tarea->setNombre($data['nombre']);
        $tarea->setDescripcion($data['descripcion']);
        $tarea->setFechaIni(\DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']));
        $tarea->setFechaFin(\DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_fin']));
        $tarea->setEstado(Estado::from($data['estado']));
        $tarea->setProyecto($proyecto);
        if (!$this->addConsultores($tarea, $consultores)) {
            throw new ConsultorNotFoundException("Alguno de los consultores proporionados no existe");
        }
        $this->tareaRepository->validateExistentTarea($tarea->getNombre(), $proyecto);
        $this->tareaRepository->save($tarea);
        return $tarea;
    }



    private function addConsultores(Tarea $tarea, array $consultores): bool
    {
        foreach ($consultores as $email_consultor) {
            $consultor = $this->consultorRepository->validateConsultor($email_consultor);
            $tarea->addConsultor($consultor);
        }
        return true;
    }

}