<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion;

use App\Usuarios\Application\Services\Notificacion\NotifiacionesDeleteService;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\PathParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/notificacion/delete/{id}', name: 'notificacion_delete', methods: ['DELETE'])]
class DeleteNotificacionController extends AbstractController
{
    #[OA\Tag(name: 'Notificaciones')]
    #[OA\Delete(
        path: '/api/notificacion/delete/{id}',
        description: 'Elimina una notificación por su ID. Requiere autenticación y autorización sobre la notificación.',
        summary: 'Eliminar una notificación específica',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de la notificación a eliminar.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Notificación eliminada con éxito (Sin contenido).'),
            new OA\Response(response: 403, description: 'Prohibido (sin permisos para eliminar esta notificación).'),
            new OA\Response(response: 404, description: 'La notificacion no existe',
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
        ]
    )]
    public function __invoke(NotifiacionesDeleteService $deleteServie, #[PathParameter] NotificacionId $id): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        return $deleteServie($usuario, $id);
    }

}