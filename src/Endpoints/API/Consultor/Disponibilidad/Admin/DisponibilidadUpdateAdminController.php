<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadUpdateAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/disponibilidad/update', name: 'admin_disponibilidad_update', methods: ['PUT'])]
class DisponibilidadUpdateAdminController extends AbstractController
{
    #[OA\Put(
        path: '/api/admin/disponibilidad/update',
        summary: 'Actualizar la disponibilidad del usuario a raiz del email del consultor y la fecha de inicio de la disponibilidad(Solo admins)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ini','consultor'],
                properties: [
                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                    new OA\Property(property: 'consultor', type: 'string', example: 'consultor@prueba.es'),
                    new OA\Property(property: 'disponible', type: 'boolean', example: false),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-04-25 15:30:00'),

                ],
                type: 'object'
            )
        ),
        tags: ['Disponibilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Disponibilidad actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'La disponibilidad se ha actualizado correctamente'),
                        new OA\Property(
                            property: 'actualizacion',
                            properties: [
                                new OA\Property(property: 'disponible', type: 'boolean', example: false),
                                new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-14 15:30:00'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, DisponibilidadUpdateAdminService $updateService):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $updateService($data);
    }

}