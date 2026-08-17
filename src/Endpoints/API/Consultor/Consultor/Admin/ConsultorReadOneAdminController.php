<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor\Admin;

use App\Consultores\Application\Services\Consultor\Admin\ConsultorReadByEmailService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/consultor/read', name: 'read_consultor_admin', methods: ['GET'])]
class ConsultorReadOneAdminController extends  AbstractController
{
    #[OA\Get(
        path: "/api/admin/consultor/read",
        description: "Obtiene los datos de un consultor mediante su correo electrónico (Admin).",
        summary: "Leer consultor por email (Admin)",
        tags: ["Consultor", "Admin"],
        parameters: [
            new OA\Parameter(
                name: "email",
                description: "Correo electrónico del consultor",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "carlos@prueba.com")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultor obtenido exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Datos del consultor"),
                        new OA\Property(property: "email", type: "string", example: "carlos@prueba.com"),
                        new OA\Property(property: "nombre", type: "string", example: "Carlos"),
                        new OA\Property(property: "apellidos", type: "string", example: "Flores"),
                        new OA\Property(property: "habilidades", type: "array", items: new OA\Items(type: "string"), example: ["Symfony", "PHP"]),
                        new OA\Property(property: "perfil", type: "string", example: "project manager"),
                        new OA\Property(property: "disponibilidad", type: "array", items: new OA\Items(type: "string"), example: ["Lunes", "Martes"])
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ConsultorReadByEmailService $readOneByEmailService, Request $request): JsonResponse
    {
        $data = ["email" => $request->query->get('email')];
        $response = $readOneByEmailService($data);
        return new JsonResponse(['message' => 'Estos son los datos del consultor: ', 'consultor' => $response], Response::HTTP_OK);

    }
}