<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad\Admin;

use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadAllServcie;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/actividad/read', name: 'actividad_read_all', methods: ['GET'])]
class ActividadReadAllController extends AbstractController
{
    #[OA\Get(
        path: '/api/admin/actividad/read',
        description: 'Este endpoint devuelve una lista con todas las actividades de la base de datos (solo admins)',
        summary: 'Obtener todas las actividades',
        tags: ["Actividad"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de actividades',
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
            new OA\Response(response: 404, description: 'El proyecto no existe'),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ActividadReadAllServcie $readAllActividadService):JsonResponse
    {
        return $readAllActividadService();
    }

}