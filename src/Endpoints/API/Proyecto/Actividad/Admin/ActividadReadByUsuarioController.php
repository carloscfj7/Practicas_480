<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad\Admin;

use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadByUsuarioService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/actividad/read/usuario', name: 'read_actividad_by_usuario', methods: ['GET'])]
class ActividadReadByUsuarioController  extends AbstractController
{
    #[OA\Get(
        path: '/api/admin/actividad/read/usuario',
        description: 'Este endpoint devuelve una lista de actividades asociadas a un usuario(Solo admins)',
        summary: 'Obtener todas las actividades de un usuario específico',
        tags: ["Actividad"],
        parameters: [
            new OA\QueryParameter(
                name: "usuario",
                description: "Email del usuario",
                required: true,
                example: "usuario@email.com",
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de actividades del usuario',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'nombre', type: 'string', example: 'Diseño de interfaz'),
                            new OA\Property(property: 'descripcion', type: 'string', example: 'Crear prototipos de pantalla'),
                            new OA\Property(property: 'fecha', type: 'string', format: 'date', example: '2025-04-01'),
                            new OA\Property(property: 'proyecto', type: 'string', example: 'Proyecto 1')
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 404, description: 'El consultor no existe'),
            new OA\Response(response: 500, description: 'Error interno del servidor')
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, ActividadReadByUsuarioService $readService)
    {
        $data = ["usuario" => $request->query->get("usuario")];
        return $readService($data);
    }

}