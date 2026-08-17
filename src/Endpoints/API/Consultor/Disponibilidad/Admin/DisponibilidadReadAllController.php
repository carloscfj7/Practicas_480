<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadReadAllAdminService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/disponibilidad/read/all', name: 'api_disponibilidad', methods: ['GET'])]
class DisponibilidadReadAllController extends AbstractController
{
    #[OA\Get(
        path: '/api/admin/disponibilidad/read/all',
        summary: 'Leer todas las disponibildiades(Solo admin)',
        tags: ['Disponibilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de disponibilidades ',
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
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(DisponibilidadReadAllAdminService $readService):JsonResponse
    {
        return $readService();
    }
}