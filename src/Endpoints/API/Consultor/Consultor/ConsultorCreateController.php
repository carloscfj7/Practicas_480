<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorCreateRequestDto;
use App\Consultores\Application\Services\Consultor\ConsultorCreateService;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/consultor/create', name: 'create_consultor', methods: 'POST')]
class ConsultorCreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/consultor/create",
        description: "Crea un nuevo consultor.",
        summary: "Crear consultor",
        requestBody: new OA\RequestBody(
            description: "Datos del consultor para crear",
            content: new OA\JsonContent(
                required: ["nombre", "apellidos", "email", "perfil", "habilidades", "disponibilidad"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Carlos"),
                    new OA\Property(property: "apellidos", type: "string", example: "Flores"),
                    new OA\Property(property: "email", type: "string", example: "carlos@prueba.com"),
                    new OA\Property(property: "password", type: "string", example: "password"),
                    new OA\Property(property: "perfil", type: "string", example: "project manager"),
                    new OA\Property(
                        property: "habilidades",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "nombre", type: "string", example: "PHP"),
                                new OA\Property(property: "nivel", type: "string", example: "alto"),
                            ],
                            type: "object"
                        )
                    )],
            )
        ),
        tags: ["Consultor"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Consultor creado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultor creado correctamente"),
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 409, description: "Ya existe un consultor con ese email"),
        ]
    )]
    public function __invoke(Request $request, ConsultorCreateService $consultorCreateService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->validateRequiredData($data);
        $data = new ConsultorCreateRequestDto($data['email'], $data['password'], $data['nombre'], $data['apellidos'], Perfil::fromString($data['perfil']), $data['habilidades']);
        $response = $consultorCreateService($data);
        return new JsonResponse(["message" => $response->message, "email" => $response->email], Response::HTTP_CREATED);
    }

    private function validateRequiredData(array $data): void
    {
        if (!isset($data['nombre'], $data['apellidos'], $data['perfil'], $data['email'], $data['password'])) {
            throw new RequiredDataException();
        }
    }
}