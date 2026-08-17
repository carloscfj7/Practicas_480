<?php

declare(strict_types=1);

namespace App\Endpoints\API\Usuarios\Usuarios\Usuario;

use App\Usuarios\Application\Services\Usuario\UsuarioDeleteService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/delete', name: 'delete', methods: ['DELETE'])]

class UsuarioDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/delete",
        summary: "Eliminar usuario",
        security: [["Bearer" => []]],
        tags: ["Usuarios"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuarios eliminado correctamente"
            ),
        ]
    )]

    public function __invoke(UsuarioDeleteService $deleteService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }
        return  $deleteService($usuario);
    }
}