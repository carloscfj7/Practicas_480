<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad;

use App\Proyectos\Application\Services\Actividad\ActividadDeleteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/actividad/delete', name: 'delete_actividad', methods: ['DELETE'])]
class ActividadDeleteController extends AbstractController
{
    #[OA\Delete(
        path: "/api/actividad/delete",
        description: "Elimina una actividad existente en el sistema.",
        summary: "Eliminar actividad",
        tags: ["Actividad"],
        parameters: [
            new OA\QueryParameter(
                name: "Proyecto",
                description: "Nombre del proyecto",
                required: true,
                example: "Proyecto 1",
            ),
            new OA\QueryParameter(
                name: "nombre",
                description: "nombre de la actividad",
                required: true,
                example: "Actividad 1",
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Activiadad eliminado exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "La actividad no existe"),
        ]
    )]
    public function __invoke(Request $request, ActividadDeleteService $deleteService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return $this->json(['error' => 'No se ha encontrado el usuario'], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["proyecto" => $request->query->get('proyecto'), "nombre" => $request->query->get('nombre')];
        return $deleteService($data, $usuario);
    }
}