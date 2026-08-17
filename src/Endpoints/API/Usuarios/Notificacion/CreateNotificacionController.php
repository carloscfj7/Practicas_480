<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Notificacion;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Application\Services\Notificacion\NotificacionCreateService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/notificacion/create', name: 'notificacion_create', methods: ['POST'])]
class CreateNotificacionController extends AbstractController
{
    #[OA\Tag(name: 'Notificaciones')]
    #[OA\Post(
        path: '/api/notificacion/create',
        operationId: 'createNotificacion',
        description: 'Crea una nueva notificación asignando al usuario autenticado como creador.',
        summary: 'Crear una nueva notificación',
        requestBody: new OA\RequestBody(
            description: 'Datos necesarios para crear la notificación.',
            required: true,
            content: new OA\JsonContent(
                required: ['mensaje', 'usuarios'],
                properties: [
                    new OA\Property(property: 'mensaje', description: 'El contenido del mensaje de la notificación.', type: 'string', example: 'Recordatorio de reunión'),
                    new OA\Property(
                        property: 'usuarios',
                        description: 'Array con los IDs de los usuarios destinatarios.',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: "usuario1@gmail.com")
                    )
                ],
                type: 'object'

            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Notificación creada con éxito.',
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
        ]
    )]
    public function __invoke(Request $request, NotificacionCreateService $createService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no existe', 'status' => 'error'], Response::HTTP_BAD_REQUEST);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createService($usuario, $data);
    }
}