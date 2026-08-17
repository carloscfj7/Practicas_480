<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto;

use App\Proyectos\Application\Services\Proyectos\ProyectoCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/proyecto/create', name: 'create_proyecto', methods: 'POST')]
class ProyectoCreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/proyecto/create",
        description: "Crea un nuevo proyecto en el sistema.",
        summary: "Crear nuevo proyecto",
        requestBody: new OA\RequestBody(
            description: "Datos necesarios para crear un nuevo proyecto",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Nuevo Proyecto"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción detallada del nuevo proyecto"),
                    new OA\Property(property: "fecha_ini", type: "string", format: "date-time", example: "2025-04-01"),
                    new OA\Property(property: "fecha_fin", type: "string", format: "date-time", example: "2025-04-30"),
                    new OA\Property(property: "estado", type: "string", example: "en proceso"),
                    new OA\Property(property: "email_cliente", type: "string", example: "cliente@cliente.com"),
                    new OA\Property(
                        property: "consultores",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "email", type: "string", example: "consultor@consultor.com"),
                            ],
                            type: "object"
                        )
                    )
                ],
                type: "object"
            )
        ),
        tags: ["Proyecto"],
        responses: [
            new OA\Response(response: 201, description: "Proyecto creado exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 409, description: "ya existe un proyecto con ese nombre"),

        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, ProyectoCreateService $createService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createService($data);

    }
}