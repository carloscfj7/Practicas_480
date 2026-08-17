<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadReadByConsultorAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/disponibilidad/read/consultor', name: 'admin_disponibilidad_consultor', methods: ['GET'])]
class DisponibilidadReadByConsultorAdminController extends AbstractController
{
    #[OA\Get(
        path: '/api/admin/disponibilidad/read/consultor',
        summary: 'Leer todas las disponibildiades de un consultor proporcionando su email(solo admins)',
        tags: ['Disponibilidad'],
        parameters: [
            new OA\QueryParameter(
                name: "consultor",
                description: "Email del consultor",
                required: true,
                example: "consultor@prueba.com",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de disponibilidades de un consultor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'disponibilidades',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 2),
                                    new OA\Property(property: 'disponible', type: 'boolean', example: true),
                                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-24 15:30:00'),
                                    new OA\Property(property: 'consultor', type: 'string', example: 'jdoe@example.com')
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'El consultor no existe'),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, DisponibilidadReadByConsultorAdminService $readService): JsonResponse
    {
        $data = ["consultor" => $request->query->get('consultor')];
        return $readService($data);
    }
}