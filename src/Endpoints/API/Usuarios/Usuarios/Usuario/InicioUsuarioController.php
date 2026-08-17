<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Usuarios\Usuario;

use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Exceptions\Usuario\InvalidCredentialsException;
use App\Usuarios\Application\Services\Usuario\InicioUsuarioService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route('/login', name: 'api_login_check', methods: ['POST'])]
class InicioUsuarioController extends AbstractController
{
    #[OA\Post(
        path: "/api/login",
        description: "Permite al usuario iniciar sesión con sus credenciales (email y password).",
        summary: "Iniciar sesión",
        security: [["Bearer" => []]],
        requestBody: new OA\RequestBody(
            description: "Datos para iniciar sesión",
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
            new OA\Response(response: 200, description: "Inicio de sesión exitoso"),
            new OA\Response(response: 400, description: "Credenciales incorrectas o usuario no registrado"),
        ]
    )]
    public function __invoke(Request $request, InicioUsuarioService $inicioUsuarioService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                throw new NoJsonProvidedException();
            }
            $this->validateRequiredData($data);

            $data = new CredentialsDto($data['email'], $data['password']);
            $response = $inicioUsuarioService($data);
            return new JsonResponse(['message' => $response->message, 'token' => $response->token], Response::HTTP_OK);

        } catch (AuthenticationException $e) {
            throw new InvalidCredentialsException();
        }
    }

    private function validateRequiredData(array $data): void{
        if (!isset($data['email']) || !isset($data['password'])) {
            throw new RequiredDataException();
        }
    }
}