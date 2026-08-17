<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad\Admin;

use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadByProyectoAndNombreService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/actividad/read/proyecto', name: 'actividad_read_proyecto_nombre', methods: ['GET'])]
class ActividadReadByProyectoAndNombreController extends AbstractController
{
    #[OA\Get(
        path: '/api/admin/actividad/read/proyecto',
        summary: 'Obtener datos de una actividad a raiz de un proyecto y el nombre de la actividad (Solo admins)',
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
            new OA\Response(
                response: 200,
                description: 'Datos de la actividad',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'nombre', type: 'string', example: 'Planificación de sprint'),
                        new OA\Property(property: 'descripcion', type: 'string', example: 'Reunión para planificar el próximo sprint'),
                        new OA\Property(property: 'fecha', type: 'string', format: 'date-time', example: '2025-04-07T09:00:00Z'),
                        new OA\Property(property: 'proyecto', type: 'string', example: 'Proyecto 1')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'El proyecto no existe'
            ),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, ActividadReadByProyectoAndNombreService $readService): JsonResponse
    {
        $data = ["proyecto" => $request->query->get("proyecto"), "nombre" => $request->query->get("nombre")];
        return $readService($data);
    }

}