<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorUpdateRequestDto;
use App\Consultores\Application\Services\Consultor\ConsultorUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/consultor/update', name: 'update_consultor', methods: 'PUT')]
class ConsultorUpdateController extends AbstractController
{
    #[OA\Put(
        path: "/api/consultor/update",
        description: "Actualiza la información del consultor autenticado.",
        summary: "Actualizar consultor",
        requestBody: new OA\RequestBody(
            description: "Datos del consultor a actualizar",
            required: true,
            content: new OA\JsonContent(
                properties: [
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
                    ),
                    new OA\Property(
                        property: "borrar_habilidades",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "nombre", type: "string", example: "Python"),
                                new OA\Property(property: "nivel", type: "string", example: "bajo"),
                            ],
                            type: "object"
                        )
                    )
                ],

            )

        ),
        tags: ["Consultor"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Perfil actualizado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Perfil actualizado correctamente"),
                        new OA\Property(property: "nuevo_perfil", type: "string", example: "desarrollador")
                    ]
                )
            ),
        ]
    )]
    public function __invoke(ConsultorUpdateService $updateService, Request $request): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $data = new ConsultorUpdateRequestDto($data['perfil'] ?? null, $data['habilidades'] ?? [], $data['borrar_habilidades'] ?? []);
        $reponse = $updateService($usuario, $data);
        return new JsonResponse(['message' => $reponse->message, 'actualizacion' => $reponse->actualizacion], Response::HTTP_OK);
    }
}