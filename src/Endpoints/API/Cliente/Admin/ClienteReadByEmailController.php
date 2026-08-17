<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente\Admin;

use App\Clientes\Application\Services\Admin\ClienteReadByEmailService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/cliente/read', name: 'read_cliente_admin', methods: ['GET'])]
class  ClienteReadByEmailController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/cliente/read",
        description: "Permite a un administrador obtener los datos de un cliente proporcionando su email.",
        summary: "Obtener datos de un cliente por email (admin)",
        tags: ["Cliente"],
        parameters: [
            new OA\QueryParameter(
                name: "email",
                description: "Email del cliente",
                required: true,
                example: "cliente@prueba.com",
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Datos del cliente obtenidos correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example:1),
                        new OA\Property(property: "email", type: "string", example: "cliente@empresa.com"),
                        new OA\Property(property: "nombre", type: "string", example: "Ana"),
                        new OA\Property(property: "contacto", type: "string", example: "123456789"),
                        new OA\Property(property: "direccion", type: "string", example: "Calle 2"),
                        new OA\Property(property: "status", type: "integer", example: 200),

                    ]
                )
            ),
            new OA\Response(response: 404, description: "El cliente no existe"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")

        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ClienteReadByEmailService $readService,Request $request): JsonResponse
    {
        $data = ["email" =>$request->query->get('email')];
        $this->validateRequiredData($data);
        $response = $readService($data);
        return new JsonResponse(['message' => 'Los datos del cliente son: ',"cliente" => $response],Response::HTTP_OK);
    }

    private function validateRequiredData(array $data) {
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }
}