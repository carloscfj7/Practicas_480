<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Usuarios\Admin;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\Admin\CreateAdminService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/register/admin', name: 'create_admin', methods: ['POST'])]
class CreateAdminController extends  AbstractController
{
    #[OA\Post(
        path: "/api/register/admin",
        description: "Permite al crear una cuenta de administrador utilizano las credenciales correctas (email y password)",
        summary: "Crear cuenta administrador",
        security: [["Bearer" => []]],
        requestBody: new OA\RequestBody(
            description: "Datos para crear la cuentade administrador",
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "usuario@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123")
                ],
                type: "object"
            )
        ),
        tags: ["Usuarios"],
        responses: [
            new OA\Response(response: 200, description: "Cuenta creada correctamente"),
            new OA\Response(response: 409, description: "Email ya registrado"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
        ]
    )]
    public function __invoke(CreateAdminService $adminService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->validateRequiredData($data);
        $data = new CredentialsDto($data['email'], $data['password']);
        $response = $adminService($data);
        return new JsonResponse(['message' => $response->message, 'email' => $response->email], Response::HTTP_CREATED);

    }
    private function validateRequiredData(array $data):void
    {
        if (!isset($data['email'], $data['password'])) {
            throw new RequiredDataException();
        }
    }

}