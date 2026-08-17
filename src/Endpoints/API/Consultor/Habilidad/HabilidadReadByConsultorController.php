<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadReadByConsultorService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/habilidad/read/consultor', name: 'habilidad_read_by_consultor', methods: ['GET'])]
class HabilidadReadByConsultorController extends AbstractController
{
    #[OA\Get(
        path: '/api/habilidad/read/consultor',
        description: 'Devuelve una lista de habilidades asociadas a un consultor específico por su email.',
        summary: 'Obtiene las habilidades de un consultor',
        tags: ['Habilidad'],
        parameters: [
            new OA\QueryParameter(
                name: "consultor",
                description: "Email del consultor",
                required: true,
                example: "consultor@email.com",
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Habilidades del consultor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'habilidades',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'nombre', type: 'string', example: 'Symfony'),
                                    new OA\Property(property: 'nivel', type: 'string', example: 'Avanzado'),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'El consultor no existe',)
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, HabilidadReadByConsultorService $readService):JsonResponse
    {
        $data = ["consultor" => $request->query->get("consultor")];
        return $readService($data);
    }

}