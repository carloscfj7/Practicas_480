<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea\Consultor;

use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadAllConsultorService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tarea/read/consultor/all', name: 'readAll_tarea_consultor', methods: ['GET'])]
class TareaReadAllByConsultorController extends AbstractController
{
    #[OA\Get(
        path: "/api/tarea/read/consultor/all",
        description: "Obtiene todas las tareas asignadas al consultor autenticado.",
        summary: "Leer todas las tareas del consultor",
        tags: ["Tarea"],
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
            )
        ]
    )]

    #[IsGranted('ROLE_CONSULTOR')]
    public function __invoke(TareaReadAllConsultorService $readService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(["error" => "El usuario no está autenticado.", 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        return $readService($usuario);
    }
}