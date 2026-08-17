<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Application\Services\Notificacion\NotificacionesReadByFechaUsuarioService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/notificacion/read/usuario/fecha', name: 'read_usuarios_fecha', methods: ['GET'])]
class ReadNotificacionByFechaUsuarioController extends AbstractController
{
    #[OA\Tag(
        name: 'Notificaciones',
        description: 'Operaciones relacionadas con las notificaciones de usuario'
    )]
    #[OA\Get(
        path: '/api/notificacion/read/usuario/fecha',
        description: 'Recupera una lista de notificaciones dirigidas al usuario actualmente autenticado, filtradas por la fecha proporcionada en el cuerpo de la solicitud (formato YYYY-MM-DD).',
        summary: 'Obtener notificaciones recibidas por el usuario autenticado en una fecha específica',
        parameters: [
            new OA\QueryParameter(
                name: "fecha",
                description: "Fecha de la notificacion",
                required: true,
                example: "2025-04-28",
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
            new OA\Response(response: 400, description: 'El formato de la fecha no es el correcto o faltan datos obligatorios'),
            new OA\Response(response: 404, description: 'El usuario no existe')
        ],
    )]
    public function __invoke(Request $request, NotificacionesReadByFechaUsuarioService $readService):JsonResponse
    {
        $usuario = $this->getUser();
        if(!$usuario instanceof Usuario){
            return new JsonResponse(['message' => 'No se ha podido obtener el usuario autenticado','status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["fecha" => $request->query->get('fecha')];
        return $readService($usuario, $data);
    }

}