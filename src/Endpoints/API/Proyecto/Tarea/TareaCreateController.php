<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea;

use App\Proyectos\Application\Services\Tareas\TareaCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/tarea/create', name: 'create_tarea', methods: ['POST'])]
class TareaCreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/tarea/create",
        description: "Crea una nueva tarea para un proyecto especificado.",
        summary: "Crear nueva tarea",
        requestBody: new OA\RequestBody(
            description: "Datos necesarios para crear una tarea",
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'descripcion', 'fecha_inicio', 'fecha_limite', 'fecha_fin', 'estimacion', 'proyecto', 'consultores'],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Tarea de ejemplo"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción de la tarea"),
                    new OA\Property(property: "fecha_inicio", type: "string", format: "date-time", example: "2025-04-01"),
                    new OA\Property(property: "fecha_fin", type: "string", format: "date-time", example: "2026-01-01"),
                    new OA\Property(property: "proyecto", type: "string", example: "Proyecto 1"),
                    new OA\Property(
                        property: "consultores",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "email", type: "string", example: "consultor@example.com"),
                            ],
                            type: "object"
                        )
                    )
                ],
                type: "object"
            )
        ),
        tags: ["Tarea"],
        responses: [
            new OA\Response(response: 201, description: "Tarea creada correctamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 409, description: "Ya existe una tarea con ese nombre en este proyecto"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(TareaCreateService $createService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createService($data);
    }
}