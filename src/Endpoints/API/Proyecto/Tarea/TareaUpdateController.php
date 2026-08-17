<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea;

use App\Proyectos\Application\Services\Tareas\TareaUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/tarea/update', name: 'update_tarea', methods: ['PUT'])]
class TareaUpdateController extends AbstractController
{
    #[OA\Put(
    path: "/api/tarea/update",
    description: "Actualiza la información de una tarea existente en el sistema.",
    summary: "Actualizar tarea",
    requestBody: new OA\RequestBody(
        description: "Datos necesarios para actualizar una tarea",
        required: true,
        content: new OA\JsonContent(
            required: ['nombre'],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Tarea importante"),
                    new OA\Property(property: "proyecto", type: "string", example: "Nombre del proyecto"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción detallada de la tarea"),
                    new OA\Property(property: "estado", type: "string", example: "En progreso"),
                    new OA\Property(property: "fecha_fin", type: "string", format: "date-time", example: "2025-04-16T18:00:00"),
                    new OA\Property(
                        property: "añadir_consultores",
                        type: "array",
                        items: new OA\Items(

                            properties: [
                                new OA\Property(property: "email", type: "string", example: "JuanPérez@perez.com"),
                            ],
                            type: "object"
                        )
                    ),
                    new OA\Property(
                        property: "borrar_consultores",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "email", type: "string", example: "JuanPérez@perez.com"),
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
        new OA\Response(response: 200, description: "Tarea actualizada exitosamente"),
        new OA\Response(response: 400, description: "Faltan datos obligatorios"),
        new OA\Response(response: 404, description: "La tera no existe"),
    ]
)]
#[IsGranted('ROLE_ADMIN')]

    public function __invoke(TareaUpdateService $updateService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $updateService($data);
    }

}