<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor;

use App\Consultores\Application\Services\Consultor\ConsultorReadService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/consultor/read', name: 'read_consultor', methods: 'GET')]
class ConsultorReadController extends AbstractController
{
    #[OA\Get(
        path: "/api/consultor/read",
        description: "Obtiene la información del consultor autenticado.",
        summary: "Leer consultor",
        tags: ["Consultor"],
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
                        new OA\Property(property: "habilidades", type: "object", example: new \stdClass(), additionalProperties: true),
                        new OA\Property(property: "perfil", type: "string", example: "project manager"),
                        new OA\Property(property: "disponibilidad", type: "object", example: new \stdClass(), additionalProperties: true)
                    ]
                )
            ),
        ]
    )]
    public function __invoke(ConsultorReadService $readService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }
        $response = $readService($usuario);
        return new JsonResponse(["message" => "Datos del consultor ", "consultor" => $response], Response::HTTP_OK);

    }
}