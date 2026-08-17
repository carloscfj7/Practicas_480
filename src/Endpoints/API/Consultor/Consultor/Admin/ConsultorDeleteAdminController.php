<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor\Admin;

use App\Consultores\Application\Services\Consultor\Admin\ConsultorDeleteByEmailService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/consultor/delete', name: 'delete_consultor_admin', methods: ['DELETE'])]
class ConsultorDeleteAdminController extends AbstractController
{
    #[OA\Delete(
        path: "/api/admin/consultor/delete",
        description: "Elimina un consultor por su correo electrónico (Admin).",
        summary: "Eliminar un consultor (Admin)",
        tags: ["Consultor", "Admin"],
        parameters: [
            new OA\QueryParameter(
                name: "email",
                description: "Email del consultor",
                required: true,
                example: "consultor@prueba.com",
            ),

        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultor eliminado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultor eliminado correctamente")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El consultor no existe"),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ConsultorDeleteByEmailService $deleteConsultorService, Request $request): Response
    {
        $email = $request->query->get('email');
        if(!$email){
            throw new RequiredDataException();
        }
        $data = ["email" =>$request->query->get('email')];
        $response = $deleteConsultorService($data);
        return new JsonResponse(['message' => $response], Response::HTTP_OK);

    }

}