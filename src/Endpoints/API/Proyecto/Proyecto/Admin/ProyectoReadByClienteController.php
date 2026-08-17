<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto\Admin;

use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadByClienteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/proyecto/read/cliente', name: 'readByCliente_proyecto', methods: 'GET')]
class ProyectoReadByClienteController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/proyecto/read/cliente",
        description: "Obtiene la lista de proyectos asociados a un cliente específico. Accesible solo por administradores.",
        summary: "Leer proyectos por cliente (Admin)",
        tags: ["Proyecto"],
        parameters: [
            new OA\Parameter(
                name: "email",
                description: "email del cliente cuyos proyectos se quieren consultar",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "cliente@cliente.com")
            ),
        ],
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
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El cliente no existe")

        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ProyectoReadByClienteService $readService, Request $request):JsonResponse
    {
        $data = ['email' => $request->query->get('email')];
        return $readService($data);

    }
}