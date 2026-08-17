<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadCreateAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/disponibilidad/create', name: 'admin_disponibilidad_create', methods: ['POST'])]
class DisponibilidadCreateAdminController extends AbstractController
{
    #[OA\Post(
        path: '/api/admin/disponibilidad/create',
        summary: 'Crear una disponibilidad para un consultor (Solo admin)',
        requestBody: new OA\RequestBody(
            description: 'Datos necesarios para crear la disponibilidad de un consultor',
            required: true,
            content: new OA\JsonContent(
                required: ['consultor', 'fecha_ini', 'fecha_fin', 'disponible'],
                properties: [
                    new OA\Property(property: 'consultor', type: 'string', example: 'consultor@email.com'),
                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-14 15:30:00'),
                    new OA\Property(property: 'disponible', type: 'boolean', example: true)
                ],
                type: 'object'
            )
        ),
        tags: ['Disponibilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Disponibilidad creada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Disponibilidad creada correctamente'),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan datos obligatorios o formato inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Faltan campos obligatorios')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Consultor no encontrado'),
            new OA\Response(response: 409, description: 'Ya existe la disponibilidad')
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]

    public function __invoke(Request $request, DisponibilidadCreateAdminService $createDisponibilidadService)
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createDisponibilidadService($data);
    }
}