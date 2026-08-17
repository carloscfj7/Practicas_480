<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Usuarios\Usuario;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/register', name: 'register', methods: ['POST'])]
class RegistroUsuarioController extends AbstractController
{
    #[OA\Post(
        path: "/api/register",
        description: "Permite registrar un nuevo usuario en la plataforma.",
        summary: "Registrar un nuevo usuario",
        security: [["Bearer" => []]],
        requestBody: new OA\RequestBody(
            description: "Datos requeridos para el registro",
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "usuario@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                    new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"), example: ["ROLE_USER"])
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
    public function __invoke(Request $request, RegistroUsuarioService $registroUsuarioService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->validateRequiredData($data);
        $data = new CredentialsDto($data['email'], $data['password'], $data['roles'] ?? []);
        $response = $registroUsuarioService($data);
        return new JsonResponse(['message' => $response->message, 'email' => $response->email], Response::HTTP_CREATED);
    }

    private function validateRequiredData(array $data):void {
        if (empty($data['email']) || empty($data['password'])) {
            throw new RequiredDataException('El email y la contraseña son requeridos');
        }
    }
}