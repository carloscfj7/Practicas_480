<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Tarea\Consultor;

use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadByProyectoAndNameConsultorService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/tarea/read/consultor/proyecto', name: 'readOneProyecto_tarea_consultor', methods: ['GET'])]
class TareaReadOneByConsultorProyectoController extends AbstractController
{
    #[OA\Get(
        path: "/api/tarea/read/consultor/proyecto",
        description: "Obtiene las tareas de un consultor relacionadas con un proyecto y nombre específicos.",
        summary: "Leer tareas por proyecto y nombre para consultor",
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
    #[IsGranted('ROLE_CONSULTOR')]


public function __invoke(TareaReadByProyectoAndNameConsultorService $readService, Request $request): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(["error" => "El usuario no está autenticado.", 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["nombre" => $request->query->get("nombre"), "proyecto" => $request->query->get("proyecto")];
        return $readService($data, $usuario);
    }
}

