<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadReadAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/habilidad/read/all', name: 'habilidad_read_all', methods: ['GET'])]
class HabilidadReadAllController extends AbstractController
{
    #[OA\Get(
        path: '/api/habilidad/read/all',
        description: 'Devuelve un array con todas las habilidades registradas en el sistema.(Solo admins)',
        summary: 'Listar todas las habilidades',
        tags: ['Habilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de habilidades',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'nombre', type: 'string', example: 'PHP'),
                            new OA\Property(property: 'nivel', type: 'string', example: 'Avanzado')
                        ],
                        type: 'object'
                    )
                )
            ),
        ]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(HabilidadReadAllService $readService)
    {
        return $readService();
    }
}