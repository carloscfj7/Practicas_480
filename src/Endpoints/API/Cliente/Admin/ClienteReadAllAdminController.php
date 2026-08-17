<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente\Admin;

use App\Clientes\Application\Services\Admin\ClienteReadAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/cliente/read/all', name: 'read_all_cliente_admin', methods: ['GET'])]
class ClienteReadAllAdminController extends AbstractController
{
    #[OA\Get(
        path: "/api/admin/cliente/read/all",
        description: "Devuelve un listado completo de los clientes registrados. Solo accesible por un administrador.",
        summary: "Listar todos los clientes (admin)",
        tags: ["Cliente"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado de clientes",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example:1),
                            new OA\Property(property: "email", type: "string", example: "cliente@empresa.com"),
                            new OA\Property(property: "nombre", type: "string", example: "Ana"),
                            new OA\Property(property: "contacto", type: "string", example: "123456789"),
                            new OA\Property(property: "direccion", type: "string", example: "Calle 2"),
                        ],
                        type: "object"
                    )
                )
            )
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ClienteReadAllService $readAllClienteService): JsonResponse
    {
        return $readAllClienteService();
    }
}