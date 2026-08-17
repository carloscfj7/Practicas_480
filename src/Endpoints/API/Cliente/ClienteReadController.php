<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente;

use App\Clientes\Application\Services\ClienteReadService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/cliente/read', name: 'read_cliente', methods: 'GET')]
class ClienteReadController extends AbstractController
{

    #[OA\Get(
        path: "/api/cliente/read",
        description: "Devuelve la información personal del cliente autenticado.",
        summary: "Obtener datos del cliente autenticado",
        tags: ["Cliente"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Datos del cliente recuperados correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Los datos del cliente son los siguientes"),
                        new OA\Property(property: "email", type: "string", example: "cliente@empresa.com"),
                        new OA\Property(property: "nombre", type: "string", example: "María"),
                        new OA\Property(property: "contacto", type: "string", example: "325233442"),
                        new OA\Property(property: "dirección", type: "string", example: "Calle 1"),
                        new OA\Property(property: "status", type: "integer", example: "200"),

                    ]
                )
            ),
            new OA\Response(response: 401, description: "El usuario no está autenticado"),
        ]
    )]
    public function __invoke(ClienteReadService $readService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }
        $response = $readService($usuario);
        return new JsonResponse(['message' => 'Los datos del cliente son los siguientes', 'cliente' => $response], Response::HTTP_OK);
    }
}