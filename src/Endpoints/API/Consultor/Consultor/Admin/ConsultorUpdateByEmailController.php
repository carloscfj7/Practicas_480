<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor\Admin;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorUpdateRequestAdminDto;
use App\Consultores\Application\Services\Consultor\Admin\ConsultorUpdateByEmailService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/consultor/update', name: 'upadate_consultor_admin', methods: ['PUT'])]
class ConsultorUpdateByEmailController extends AbstractController
{
    #[OA\Put(
        path: "/api/admin/consultor/update",
        description: "Actualiza los datos de un consultor mediante su email.",
        summary: "Actualizar consultor por email (Admin)",
        requestBody: new OA\RequestBody(
            description: "Datos del consultor para actualizar",
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "carlos@prueba.com"),
                    new OA\Property(property: "perfil", type: "string", example: "developer"),
                    new OA\Property(property: "habilidades", type: "array", items: new OA\Items(type: "string"), example: ["Symfony", "PHP"]),
                    new OA\Property(property: "disponibilidad", type: "array", items: new OA\Items(type: "string"), example: ["Lunes", "Martes"])
                ]
            )
        ),
        tags: ["Consultor", "Admin"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultor actualizado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Perfil actualizado correctamente"),
                        new OA\Property(property: "nuevo_perfil", type: "string", example: "project manager")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El consultor no existe"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ConsultorUpdateByEmailService $updateConsultorByEmailService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }

        $this->validateRequiredData($data);
        $data = new ConsultorUpdateRequestAdminDto($data['email'],
            $data['perfil'] ?? null, $data['habilidades'] ?? null,
            $data['borrar_habilidades'] ?? null);
        $response = ($updateConsultorByEmailService)( $data);

        return new JsonResponse(['message' => $response->message, 'actualizacion' => $response->actualizacion], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data): void
    {
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }

}