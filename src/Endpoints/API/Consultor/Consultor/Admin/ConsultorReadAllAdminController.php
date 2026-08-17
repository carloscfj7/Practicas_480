<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor\Admin;

use App\Consultores\Application\Services\Consultor\Admin\ConsultorReadAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/consultor/read/all', name: 'read_all_consultor_admin', methods: ['GET'])]
class ConsultorReadAllAdminController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/consultor/read/all",
        description: "Obtiene todos los consultores para el administrador.",
        summary: "Leer todos los consultores (Admin)",
        tags: ["Consultor", "Admin"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultores obtenidos exitosamente",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nombre", type: "string", example: "Carlos"),
                            new OA\Property(property: "apellidos", type: "string", example: "Flores"),
                            new OA\Property(property: "email", type: "string", example: "carlos@prueba.com"),
                            new OA\Property(property: "perfil", type: "string", example: "project manager"),
                            new OA\Property(property: "habilidades", type: "array", items: new OA\Items(type: "string"), example: ["Symfony", "PHP"]),
                            new OA\Property(property: "disponibilidad", type: "array", items: new OA\Items(type: "string"), example: ["Lunes", "Martes"])
                        ],
                        type: "object"
                    )
                )
            ),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ConsultorReadAllService $readAllConsultorService): JsonResponse
    {
        $response = $readAllConsultorService();
        if (!$response) {
            return new JsonResponse(['message' => 'No hay consultores'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['message' => 'Estos son todos los consultores: ', 'consultores' => $response], Response::HTTP_OK);

    }
}