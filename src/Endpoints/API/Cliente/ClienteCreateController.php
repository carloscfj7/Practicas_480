<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente;

use App\Clientes\Application\Dto\DataRequest\ClienteCreateRequestDto;
use App\Clientes\Application\Dto\DataResponse\ClienteCreateResponseDto;
use App\Clientes\Application\Services\ClienteCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/cliente/create', name: 'create_cliente', methods: 'POST')]
class ClienteCreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/cliente/create",
        description: "Crea un nuevo cliente con los datos proporcionados.",
        summary: "Crear un nuevo cliente",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre", "apellidos", "email", "password"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Juan"),
                    new OA\Property(property: "contacto", type: "string", example: "894827456"),
                    new OA\Property(property: "direccion", type: "string", example: "Dirección 1"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "juan.perez@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "securePassword123")
                ]
            )
        ),
        tags: ["Cliente"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Cliente creado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente creado correctamente"),
                        new OA\Property(property: "id", type: "integer", example: 42)
                    ]
                )
            ),
            new OA\Response(response: 409, description: "Ya existe un usuario con ese email"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ClienteCreateService $createService, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->validateRequiredData($data);
        $data = new ClienteCreateRequestDto($data['email'],
            $data['password'],
            $data['nombre'],
            $data['contacto'],
            $data['direccion'],);
        $respsonse = $createService($data);
        return new JsonResponse($respsonse, Response::HTTP_CREATED);

    }

    private function validateRequiredData(array $data)
    {
        if (!isset($data['nombre'], $data['contacto'], $data['direccion'], $data['email'], $data['password'])) {
            throw new RequiredDataException();
        }
        return null;
    }

}