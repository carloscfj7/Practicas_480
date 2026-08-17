<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea\Admin;

use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByConsultorAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/tarea/read/all/consultor', name: 'readAllConsultor_tarea_Admin', methods: ['GET'])]
class TareaReadByConsultorAdminController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/tarea/read/all/consultor",
        description: "Obtiene todas las tareas asignadas a un consultor específico. Solo disponible para administradores.",
        summary: "Leer todas las tareas de un consultor (admin)",
        tags: ["Tarea"],
        parameters: [
            new OA\Parameter(
                name: "consultor",
                description: "Email del consultor al cual están asignadas las tareas",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "consultor@example.com")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Tareas obtenidas exitosamente",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
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
                )
            ),
            new OA\Response(response: 404, description: "El consultor no existe"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]

    public function __invoke(TareaReadByConsultorAdminService $readService, Request $request): JsonResponse
    {
        $data = ["consultor" => $request->query->get("consultor")];
        return $readService($data);
    }

}