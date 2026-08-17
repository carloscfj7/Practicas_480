<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto;

use App\Proyectos\Application\Services\Proyectos\ProyectoUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/proyecto/update', name: 'update_proyecto', methods: 'PUT')]
class ProyectoUpdateController extends AbstractController
{
    #[OA\Put(
        path: "/api/proyecto/update",
        description: "Actualiza la información de un proyecto existente.",
        summary: "Actualizar Proyecto",
        requestBody: new OA\RequestBody(
            description: "Datos necesarios para actualizar un proyecto",
            required: true,
            content: new OA\JsonContent(
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Nuevo Proyecto"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción del proyecto actualizado"),
                    new OA\Property(property: "estado", type: "string", example: "En progreso"),
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
        tags: ["Proyecto"],
        responses: [
            new OA\Response(response: 200, description: "Proyecto actualizado exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El proyecto no existe"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ProyectoUpdateService $updateService, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $updateService($data);
    }
}