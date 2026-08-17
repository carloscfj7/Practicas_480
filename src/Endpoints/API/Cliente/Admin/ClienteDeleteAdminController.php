<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente\Admin;

use App\Clientes\Application\Services\Admin\ClienteDeleteByEmailService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\ValueObjects\Email;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/cliente/delete', name: 'delete_cliente_admin', methods: ['DELETE'])]
class ClienteDeleteAdminController extends AbstractController
{
    #[OA\Delete(
        path: "/api/admin/cliente/delete",
        description: "Elimina un cliente del sistema a partir de su email. Solo accesible por administradores.",
        summary: "Eliminar cliente por email (admin)",
        tags: ["Cliente"],
        parameters: [
            new OA\QueryParameter(
                name: "cliente",
                description: "Email del cliente",
                required: true,
                example: "cliente@prueba.com",
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cliente eliminado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente eliminado correctamente")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 404, description: "El cliente no existe"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")

        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ClienteDeleteByEmailService $deleteConsultorService, Request $request ): Response
    {
        $data = ["email" =>$request->query->get('email')];
        return $deleteConsultorService($data);
    }
}