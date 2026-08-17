<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadDeleteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/habilidad/delete', name: 'delete_habilidad', methods: ['DELETE'])]
class HabilidadDeleteController extends AbstractController
{
    #[OA\Delete(
        path: '/api/habilidad/delete',
        description: 'Elimina una habilidad específica identificada por su nombre y el nivel.',
        summary: 'Eliminar una habilidad',
        tags: ['Habilidad'],
        parameters: [
            new OA\QueryParameter(
                name: "nombre",
                description: "Nombre de la habilidad",
                required: true,
                example: "Php",
            ),
            new OA\QueryParameter(
                name: "nivel",
                description: "nivel de la habilidad",
                required: true,
                example: "alto",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Habilidad eliminada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habilidad eliminada correctamente')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Faltan datos obligatorios'),
            new OA\Response(response: 500, description: 'La habilidad no existe')
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, HabilidadDeleteService $deleteService):JsonResponse
    {
        $data = ["nombre" => $request->query->get('nombre'), "nivel" => $request->query->get('nivel')];
        return $deleteService($data);
    }
}