<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto\Consultor;

use App\Proyectos\Application\Services\Proyectos\Consultor\ProyectoReadOneConsultorService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/proyecto/read/consultor', name: 'readOne_proyecto_conusltor', methods: 'GET')]
class ProyectoReadOneByConsultorController extends AbstractController
{
    #[OA\Get(
        path: "/api/proyecto/read/consultor",
        description: "Obtiene la información de un proyecto para el consultor autenticado.",
        summary: "Leer proyecto por consultor",
        tags: ["Proyecto"],
        parameters: [
            new OA\Parameter(
                name: "nombre",
                description: "Nombre del proyecto a consultar",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "Proyecto Ejemplo")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Proyecto obtenid correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "nombre", type: "string", example: "Proyecto 1"),
                        new OA\Property(property: "descripcion", type: "string", example: "Descripción del proyecto"),
                        new OA\Property(property: "fecha_ini", type: "date", example: "2025-03-10"),
                        new OA\Property(property: "fecha_fin", type: "date", example: "2025-03-24"),
                        new OA\Property(property: "estado", type: "string", example: "en proceso"),
                        new OA\Property(property: "consultores", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "nombre", type: "string", example: "Juan"),
                                new OA\Property(property: "apellidos", type: "string", example: "Perez Gomez"),
                                new OA\Property(property: "perfil", type: "string", example: "desarrollador"),
                            ],
                            type: "object"
                        ))
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 404, description: "El consultor no existe"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")


        ]
    )]
    #[IsGranted('ROLE_CONSULTOR')]
    public function __invoke(ProyectoReadOneConsultorService $readService, Request $request): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(["error" => "El usuario no está autenticado.", 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["nombre" => $request->get('nombre')];
        return $readService($data, $usuario);

    }
}