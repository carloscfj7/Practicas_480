<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/habilidad/create', name: 'habilidad_create', methods: ['POST'])]
class HabilidadCreateController extends AbstractController
{
    #[OA\Post(
        path: '/api/habilidad/create',
        description: 'Permite crear una nueva habilidad (solo admins).',
        summary: 'Crear una nueva habilidad',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'nivel'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Python'),
                    new OA\Property(property: 'nivel', type: 'string', example: 'Avanzado')
                ],
                type: 'object'
            )
        ),
        tags: ['Habilidad'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Habilidad creada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habilidad creada correctamente'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Faltan datos obligatorios'),
            new OA\Response(response: 409, description: 'La habilidad ya existe'),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, HabilidadCreateService $createService):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        return $createService($data);
    }

}