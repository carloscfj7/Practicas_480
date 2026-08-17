<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto\Admin;

use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/proyecto/read/all', name: 'readAll_proyecto', methods: 'GET')]
class ProyectoReadAllAdminController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/proyecto/read/all",
        description: "Obtiene la lista de todos los proyectos registrados en el sistema. Accesible solo por administradores.",
        summary: "Leer todos los proyectos (Admin)",
        tags: ["Proyecto"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de proyectos obtenida exitosamente",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 5),
                            new OA\Property(property: "nombre", type: "string", example: "Proyecto Ejemplo 2"),
                            new OA\Property(property: "descripcion", type: "string", example: "Descripción del proyecto"),
                            new OA\Property(property: "fecha_ini", type: "string", format: "date", example: "2025-03-10"),
                            new OA\Property(property: "fecha_fin", type: "string", format: "date", example: "2025-03-24"),
                            new OA\Property(property: "estado", type: "string", example: "en proceso"),
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
                )
            ),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ProyectoReadAllService $readService):JsonResponse
    {
        return $readService();
    }
}