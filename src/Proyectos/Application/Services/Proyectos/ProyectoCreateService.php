<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos;

use App\Clientes\Application\Exceptions\ClienteNotFoundException;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Domain\Exceptions\Proyecto\ExistentProyectoException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoCreateService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ClienteRepositoryInterface $clienteRepository, private ConsultorRepositoryInterface $consultorRepository)
    {
    }


    public function __invoke(array $data): JsonResponse
    {
        $this->validateRequiredData($data);
        $this->proyectoRepository->validateExistentProyecto($data['nombre']);
        $fecha_ini = $this->validateDate($data['fecha_ini']);
        if (!empty($data['fecha_fin'])) {
            $fecha_fin = $this->validateDate($data['fecha_fin']);
            $this->validateDates($fecha_ini, $fecha_fin);
        }

        $cliente = $this->clienteRepository->validateClienteOrFails($data['email_cliente']);

        $proyecto = $this->createProyecto($data, $cliente);

        $this->consultorRepository->addConsultoresToProyecto($proyecto, $data['consultores']);

        $this->proyectoRepository->save($proyecto);

        return new JsonResponse(["message" => "El proyecto se ha creado correctamente", 'nombre' => $proyecto->getNombre()], Response::HTTP_CREATED);
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['fecha_ini']) || empty($data['estado']) || empty($data['email_cliente']) || empty($data['consultores'])) {
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

    private function validateDates(\DateTimeInterface $fecha_ini, \DateTimeInterface $fecha_fin): void
    {
        if ($fecha_ini > $fecha_fin) {
            throw new InvalidDateRangeExcpecion();
        }
    }

    private function createProyecto(array $data, Cliente $cliente): Proyecto
    {
        $proyecto = new Proyecto();
        $proyecto->setNombre($data['nombre']);
        $proyecto->setDescripcion($data['descripcion']);
        $proyecto->setFechaIni(new \DateTime($data['fecha_ini']));
        $proyecto->setFechaFin(empty($data['fecha_fin']) ? null : new \DateTime($data['fecha_fin']));
        $proyecto->setEstado(Estado::from($data['estado']));
        $proyecto->setCliente($cliente);

        return $proyecto;
    }




}