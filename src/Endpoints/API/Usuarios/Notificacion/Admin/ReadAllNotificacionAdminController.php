<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion\Admin;

use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadAllAdminService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/notificacion/read/all', name: 'readAll_notificaciones', methods: ['GET'])]
class ReadAllNotificacionAdminController extends AbstractController
{
    #[OA\Tag(
        name: 'Notificaciones',
        description: 'Operaciones relacionadas con las notificaciones de usuario'
    )]
    #[OA\Get(
        path: '/api/admin/notificacion/read/all',
        description: 'Recupera una lista de todas las notificaciones.',
        summary: 'Obtener notificaciones',
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
        ],

    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(NotificacionesReadAllAdminService $readAllNotificacionesService):JsonResponse
    {
        return $readAllNotificacionesService();
    }
}