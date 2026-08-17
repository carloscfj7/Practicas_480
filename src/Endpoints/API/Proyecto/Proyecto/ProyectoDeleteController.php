<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto;

use App\Proyectos\Application\Services\Proyectos\ProyectoDeleteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/proyecto/delete', name: 'delete_proyecto', methods: 'DELETE')]
class ProyectoDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/proyecto/delete",
        description: "Elimina un proyecto existente en el sistema.",
        summary: "Eliminar proyecto",
        tags: ["Proyecto"],
        parameters: [
            new OA\QueryParameter(
                name: "nombre",
                description: "Nombre del proyecto",
                required: true,
                example: "Proyecto 1",
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Proyecto eliminado exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El proyecto no existe"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ProyectoDeleteService $deleteService, Request $request):JsonResponse
    {
        $data = ["nombre" => $request->get("nombre")];
        return $deleteService($data);

    }
}