<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion;

use App\Usuarios\Application\Services\Notificacion\NotificacionesReadByUsuarioService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/notificacion/read/usuario', name: 'usuarios_notificaciones', methods: ['GET'])]
class ReadNotifiacionesByUsuarioController extends AbstractController
{
    #[OA\Tag(
        name: 'Notificaciones',
        description: 'Operaciones relacionadas con las notificaciones de usuario'
    )]
    #[OA\Get(
        path: '/api/notificacion/read/usuario',
        description: 'Recupera una lista de las notificacciones recibidas por el usuario actualmente autenticado.',
        summary: 'Obtener notificaciones recibidas por el usuario autenticado',
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
            new OA\Response(response: 400, description: 'Faltan datos obligatorios'),
            new OA\Response(response: 404, description: 'El usuario no existe')
        ],

    )]
public function __invoke(NotificacionesReadByUsuarioService $readService): JsonResponse
    {
        $usuario  = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        return $readService($usuario);
    }

}