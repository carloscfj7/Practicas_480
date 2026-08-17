<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea\Admin;

use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByProyectoAndNameAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/tarea/read/proyecto', name: 'readOneProyecto_tarea_Admin', methods: ['GET'])]
class TareaReadOneByProyectoController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/tarea/read/proyecto",
        description: "Obtiene la tarea de un proyecto específico por su nombre. Solo disponible para administradores.",
        summary: "Leer tarea por proyecto y nombre (admin)",
        tags: ["Tarea"],
        parameters: [
            new OA\Parameter(
                name: "proyecto",
                description: "Nombre del proyecto al cual pertenece la tarea",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "Proyecto 1")
            ),
            new OA\Parameter(
                name: "nombre",
                description: "Nombre de la tarea a buscar",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "Tarea urgente")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Tarea obtenida correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "nombre", type: "string", example: "Tarea importante"),
                        new OA\Property(property: "descripcion", type: "string", example: "Descripción de la tarea"),
                        new OA\Property(property: "estimacion", type: "string", example: "5 días 4 horas 30 minutos"),
                        new OA\Property(
                            property: "proyecto",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "nombre", type: "string", example: "Proyecto 1")
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "consultores",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "nombre", type: "string", example: "Juan Pérez")
                                ],
                                type: "object"
                            )
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 404, description: "El proyecto no existe"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]

    public function __invoke(TareaReadByProyectoAndNameAdminService $readService, Request $request): JsonResponse
    {
        $data = ["proyecto" => $request->query->get('proyecto'), "nombre" => $request->query->get('nombre')];
        return $readService($data);
    }

}