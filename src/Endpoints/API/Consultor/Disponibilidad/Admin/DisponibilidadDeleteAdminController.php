<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadDeleteAdminService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/disponibilidad/delete', name: 'admin_disponibilidad_delete', methods: ['DELETE'])]
class DisponibilidadDeleteAdminController extends AbstractController
{
    #[OA\Delete(
        path: '/api/admin/disponibilidad/delete',
        summary: 'Eliminar la disponibilidad de un consultor con su email y la fecha dde inicio(Solo admins)',
        tags: ['Disponibilidad'],
        parameters: [
            new OA\QueryParameter(
                name: "consultor",
                description: "Email del consultor",
                required: true,
                example: "consultor@prueba.com",
            ),
            new OA\QueryParameter(
                name: "fecha_ini",
                description: "fecha de inicio de la disponibilidad",
                required: true,
                example: "2025-05-25 15:30:00",
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Disponibilidad eliminada correctamente'),
            new OA\Response(response: 404, description: 'La disponibilidad no existe')
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, DisponibilidadDeleteAdminService $deleteService)
    {
        $data = ["consultor" => $request->query->get('consultor'), "fecha_ini" => $request->query->get('fecha_ini')];
        return $deleteService($data);
    }

}