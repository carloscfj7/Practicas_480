<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente;

use App\Clientes\Application\Services\ClienteDeleteService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/cliente/delete', name: 'delete_cliente', methods: 'DELETE')]
class ClienteDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/cliente/delete",
        description: "Elimina al cliente actualmente autenticado del sistema.",
        summary: "Eliminar cliente autenticado",
        tags: ["Cliente"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cliente eliminado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente eliminado correctamente")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "El usuario no está autenticado"),]
    )]
    public function __invoke(ClienteDeleteService $deleteService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }

        $response = $deleteService($usuario);
        return new JsonResponse(["message" => $response], Response::HTTP_OK);

    }
}