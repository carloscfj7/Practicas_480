<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad;

use App\Proyectos\Application\Services\Actividad\ActividadReadService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/actividad/read', name: 'read_actividad', methods: ['GET'])]
class ActividadReadController extends AbstractController
{
    #[OA\Get(
        path: '/api/actividad/read',
        summary: 'Obtener datos de una actividad por usuario',
        tags: ["Actividad"],
        parameters: [
            new OA\QueryParameter(
                name: "Proyecto",
                description: "Nombre del proyecto",
                required: true,
                example: "Proyecto 1",
            ),
            new OA\QueryParameter(
                name: "nombre",
                description: "nombre de la actividad",
                required: true,
                example: "Actividad 1",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos de la actividad',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'nombre', type: 'string', example: 'Planificación de sprint'),
                        new OA\Property(property: 'descripcion', type: 'string', example: 'Reunión para planificar el próximo sprint'),
                        new OA\Property(property: 'fecha', type: 'string', format: 'date-time', example: '2025-04-07T09:00:00Z'),
                        new OA\Property(property: 'proyecto', type: 'string', example: 'Proyecto 1')
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request, ActividadReadService $readService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return $this->json(['error' => 'No se ha encontrado el usuario'], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["proyecto" => $request->get("proyecto"), "nombre" => $request->get("nombre")];
        return $readService($data, $usuario);
    }
}