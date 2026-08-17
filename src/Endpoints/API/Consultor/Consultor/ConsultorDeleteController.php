<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Consultor;

use App\Consultores\Application\Services\Consultor\ConsultorDeleteService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/consultor/delete', name: 'delete_consultor', methods: 'DELETE')]
class ConsultorDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/consultor/delete",
        description: "Elimina al consultor autenticado.",
        summary: "Eliminar consultor",
        tags: ["Consultor"],
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
        ]
    )]
    public function __invoke(ConsultorDeleteService $deleteService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }

        $response = $deleteService($usuario);
        return new JsonResponse(['message' => $response], Response::HTTP_OK);
    }
}