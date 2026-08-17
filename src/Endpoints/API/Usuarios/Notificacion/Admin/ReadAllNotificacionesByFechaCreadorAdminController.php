<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion\Admin;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadAllByFechaCreadorAdminService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/notificacion/read/creador/fecha', name: 'Read_notificacicon_fecha_creador_admin', methods: ['GET'])]
class ReadAllNotificacionesByFechaCreadorAdminController extends AbstractController
{
    #[OA\Tag(
        name: 'Notificaciones',
        description: 'Operaciones relacionadas con las notificaciones de usuario'
    )]
    #[OA\Get(
        path: '/api/admin/notificacion/read/creador/fecha',
        description: 'Recupera una lista de notificaciones creadas por el  usuario proprcionado filtradas por la fecha proporcionada en el cuerpo de la solicitud (formato YYYY-MM-DD).',
        summary: 'Obtener notificaciones creadas por el usuario proprocionado en una fecha específica',
        parameters: [
            new OA\QueryParameter(
                name: "email",
                description: "Email del creador",
                required: true,
                example: "creador@prueba.com",
            ),
            new OA\QueryParameter(
                name: "fecha",
                description: "fecha de la notificacion",
                required: true,
                example: "2025-05-25 15:30:00",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de notificaciones recuperada con éxito.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'mensaje', type: 'string', example: 'Contenido de la notificación'),
                            new OA\Property(property: 'fecha', type: 'string', format: 'date-time', example: '2025-05-14T10:00:00Z'),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: 'El formato de la fecha no es el correcto o faltan datos obligatorios'),
            new OA\Response(response: 404, description: 'El usuario no existe')
        ],

    )]
    #[IsGranted('ROLE_ADMIN')]
 public function __invoke(Request $request, NotificacionesReadAllByFechaCreadorAdminService $readService): JsonResponse
 {
     $data = ["email" => $request->query->get('email'), "fecha" => $request->query->get('fecha')];
     return $readService($data);
 }
}