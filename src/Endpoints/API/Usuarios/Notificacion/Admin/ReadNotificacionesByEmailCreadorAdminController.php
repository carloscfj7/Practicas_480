<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion\Admin;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Application\Services\Notificacion\Admin\NotificacionesReadByEmailCreadorAdminService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/notificacion/read/creador', name: 'read_notificaciones_by_email_creador', methods: ['GET'])]
class ReadNotificacionesByEmailCreadorAdminController extends AbstractController
{
    #[OA\Tag(
        name: 'Notificaciones',
        description: 'Operaciones relacionadas con las notificaciones de usuario'
    )]
    #[OA\Get(
        path: '/api/admin/notificacion/read/creador',
        description: 'Recupera una lista de notificaciones creadas por el usuario proprcionado.',
        summary: 'Obtener notificaciones creadas por el usuario proprocionado',
        parameters: [
            new OA\QueryParameter(
                name: "email",
                description: "Email del usuario",
                required: true,
                example: "usuario@prueba.com",
            ),
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
            new OA\Response(response: 404, description: 'El usuario no existe',)
        ],

    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, NotificacionesReadByEmailCreadorAdminService $readService):JsonResponse
    {
        $data = ["email" => $request->query->get("email")];
        return $readService($data);
    }
}