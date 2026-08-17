<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto\Admin;

use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadOneService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/proyecto/read', name: 'readOne_proyecto', methods: 'GET')]
class ProyectoReadOneAdminController extends  AbstractController
{
    #[OA\Get(
        path: "/api/admin/proyecto/read",
        description: "Obtiene la información de un proyecto específico. Solo accesible por administradores.",
        summary: "Leer un proyecto por administrador",
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
            new OA\Response(
                response: 200,
                description: "Proyecto obtenido exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "nombre", type: "string", example: "Proyecto ejemplo 2"),
                        new OA\Property(property: "descripcion", type: "string", example: "descripcion ejemplo"),
                        new OA\Property(property: "fecha_ini", type: "string", format: "date", example: "2025-03-10"),
                        new OA\Property(property: "fecha_fin", type: "string", format: "date", example: "2025-03-24"),
                        new OA\Property(property: "estado", type: "string", example: "en proceso"),
                        new OA\Property(property: "cliente_id", type: "integer", example: 4),
                        new OA\Property(
                            property: "consultores",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 6),
                                    new OA\Property(property: "nombre", type: "string", example: "Juan"),
                                    new OA\Property(property: "apellidos", type: "string", example: "Pérez Gómez"),
                                    new OA\Property(property: "perfil", type: "string", example: "project manager")
                                ],
                                type: "object"
                            )
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ProyectoReadOneService $readService, Request $request):JsonResponse
    {
        $data = ['nombre' => $request->query->get('nombre')];
        if ($data === []) {
            throw new NoJsonProvidedException();
        }
        return $readService($data);

    }
}