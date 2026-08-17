<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad;


use App\Proyectos\Application\Services\Actividad\ActividadCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/actividad/create', name: 'create_actividad', methods: ['POST'])]
class ActividadCreateController extends AbstractController
{
    #[OA\Post(
        path: "/api/actividad/create",
        description: "Crea una nueva actividad en el sistema.",
        summary: "Crear una nueva actividad",
        requestBody: new OA\RequestBody(
            description: "Datos necesarios para crear una actividad",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Nueva actividad"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción detallada de la actividad"),
                    new OA\Property(property: "fecha", type: "string", format: "date-time", example: "2025-04-01"),
                    new OA\Property(property: "proyecto", type: "string", example: "Proyecto 1"),

                ],
                type: "object"
            )
        ),
        tags: ["Actividad"],

        responses: [
            new OA\Response(response: 201, description: "Proyecto creado exitosamente"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El proyecto  o alguno de los consultores no existen"),
        ]
    )]
    public function __invoke(Request $request, ActividadCreateService $createService)
    {
        $user = $this->getUser();
        if (!$user instanceof Usuario) {
            return $this->json(['error' => 'No se ha encontrado el usuario'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createService($data, $user);

    }
}