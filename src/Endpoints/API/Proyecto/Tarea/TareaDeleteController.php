<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea;

use App\Proyectos\Application\Services\Tareas\TareaDeleteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/tarea/delete', name: 'delete_tarea', methods: ['DELETE'])]
class TareaDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/tarea/delete",
        description: "Elimina una tarea específica del sistema mediante su nombre y su proyecto.",
        summary: "Eliminar tarea",
        tags: ["Tarea"],
        parameters: [
            new OA\Parameter(
                name: "proyecto",
                description: "Nombre del proyecto para filtrar las tareas",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "nombre",
                description: "Nombre de la tarea para filtrar",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Tarea eliminada exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "La tarea no existe"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]

    public function __invoke(TareaDeleteService $deleteService, Request $request): JsonResponse
    {
        $data = ["nombre" => $request->get("nombre"), "proyecto" => $request->get("proyecto")];
        return $deleteService($data);
    }
}