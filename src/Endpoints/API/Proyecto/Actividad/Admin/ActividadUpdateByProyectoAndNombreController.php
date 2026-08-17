<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad\Admin;

use App\Proyectos\Application\Services\Actividad\Admin\ActividadUpdateByProyectoAndNombreService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/actividad/update', name: 'Actividad_update_proyecto_nombre', methods: ['PUT'])]
class ActividadUpdateByProyectoAndNombreController extends AbstractController
{
    #[OA\Put(
        path: '/api/admin/actividad/update',
        summary: 'Actualizar una actividad por su proyecto y nombre (solo admins)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['proyecto', 'nombre'],
                properties: [
                    new OA\Property(property: 'proyecto', type: 'string', example: "Proyecto 2"),
                    new OA\Property(property: 'nombre', type: 'string', example: 'Actividad 1'),
                    new OA\Property(property: 'descripcion', type: 'string', example: 'Nueva descripcion de la actividad')
                ],
                type: 'object'
            )
        ),
        tags: ["Actividad"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividad actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Nueva descripcion de la actividad'),
                        new OA\Property(property: 'actualizado', type: 'string', example: "descripcion 1")
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autorizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'No se ha encontrado el usuario')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'El proyecto o la activiad no existen',),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, ActividadUpdateByProyectoAndNombreService $updateService)
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $updateService($data);
    }
}