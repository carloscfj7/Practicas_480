<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Services\Notificacion\Admin;

use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Notificacion\NotificacionCreadorDto;
use App\Usuarios\Application\Exceptions\Usuario\UsuarioNotFoundException;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionesReadByFechaAdminService
{
    public function __construct(private NotificacionRepositoryInterface $notificacionRepository, private NotificacionCreadorDto $notificacionCreadorDto)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $fecha = $this->validateDate($data['fecha']);
        $notificaciones = $this->notificacionRepository->findByFecha($fecha);
        if ($notificaciones === [])
        {
            return new JsonResponse(['message' => 'No hay ninguna notificacion creada en la fecha proporionada'], Response::HTTP_OK);
        }
        $notificaciones = $this->notificacionCreadorDto->collectionFromEntities($notificaciones);

        return new JsonResponse(['message'=>'Estas son todas las notificaciones recibidas el dia: '. $data['fecha'],'notificaciones' => $notificaciones], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data):void{
        if (empty($data['fecha'])) {
            throw new RequiredDataException();
        }
    }
    public function validateDate(string $fecha):\DateTime{
        $converterdFecha = \DateTime::createFromFormat("Y-m-d", $fecha);
        if (!$converterdFecha) {
            throw new InvalidDateException();
        }
        return $converterdFecha;
    }

}