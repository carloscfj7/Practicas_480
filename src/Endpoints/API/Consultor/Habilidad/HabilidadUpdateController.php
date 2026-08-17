<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/habilidad/update', name: 'habilidad_update', methods: ['PUT'])]
class HabilidadUpdateController extends AbstractController
{
    #[OA\Put(
        path: '/api/habilidad/update',
        description: 'Permite actualizar el nombre o nivel de una habilidad mediante su ID.',
        summary: 'Actualiza una habilidad existente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'nivel'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'PHP'),
                    new OA\Property(property: 'nivel', type: 'string', example: 'alto'),
                    new OA\Property(property: 'nuevo_nombre', type: 'string', example: 'Python'),
                    new OA\Property(property: 'nuevo_nivel', type: 'string', example: 'medio')
                ],
                type: 'object'
            )
        ),
        tags: ['Habilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Habilidad actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'habilidad',
                            properties: [
                                new OA\Property(property: 'nombre', type: 'string', example: 'Python'),
                                new OA\Property(property: 'nivel', type: 'string', example: 'medio')
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Faltan datos obligatorios'),
            new OA\Response(response: 404, description: 'La habilidad no existe')
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, HabilidadUpdateService $updateService)
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $updateService($data);
    }
}