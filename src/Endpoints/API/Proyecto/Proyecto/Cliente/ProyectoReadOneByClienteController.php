<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Proyecto\Cliente;

use App\Proyectos\Application\Services\Proyectos\Cliente\ProyectoReadOneClienteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/proyecto/read/cliente', name: 'readOne_proyecto_cliente', methods: 'GET')]
class ProyectoReadOneByClienteController extends AbstractController
{
    #[OA\Get(
        path: "/api/proyecto/read/cliente",
        description: "Obtiene la información de un proyecto específico asociado al cliente autenticado.",
        summary: "Leer un proyecto por cliente",
        tags: ["Proyecto"],
        parameters: [
            new OA\Parameter(
                name: "nombre",
                description: "Nombre del proyecto a consultar",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "Proyecto Ejemplo")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Proyecto obtenido exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "nombre", type: "string", example: "Proyecto ejemplo 2"),
                        new OA\Property(property: "descripcion", type: "string", example: "descripcion ejemplo"),
                        new OA\Property(property: "fecha_ini", type: "string", format: "date", example: "2025-03-10"),
                        new OA\Property(property: "fecha_fin", type: "string", format: "date", example: "2025-03-24"),
                        new OA\Property(property: "estado", type: "string", example: "en proceso"),
                        new OA\Property(
                            property: "consultores",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 6),
                                    new OA\Property(property: "nombre", type: "string", example: "Juan"),
                                    new OA\Property(property: "apellidos", type: "string", example: "Pérez Gómez"),
                                    new OA\Property(property: "perfil", type: "string", example: "project manager")
                                ],
                                type: "object"
                            )
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
            new OA\Response(response: 404, description: "El cliente no existe")

        ]
    )]
    #[IsGranted("ROLE_CLIENTE")]
    public function __invoke(ProyectoReadOneClienteService $readService, Request $request):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(["error" => "El usuario no está autenticado.", 'status' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["nombre" => $request->query->get("nombre")];
        return $readService($data, $usuario);

    }
}