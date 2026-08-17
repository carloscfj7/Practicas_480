<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadReadFromUserService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/consultor/habilidad', name: 'consultor_habilidad', methods: ['GET'])]
class HabilidadReadFromUsuarioController extends AbstractController
{
    #[OA\Get(
        path: '/api/consultor/habilidad',
        description: 'Devuelve una lista de habilidades asociadas al usuario autenticado (consultor).',
        summary: 'Obtiene las habilidades del consultor autenticado',
        tags: ['Habilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de habilidades del consultor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'habilidades',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'nombre', type: 'string', example: 'Python'),
                                    new OA\Property(property: 'nivel', type: 'string', example: 'Avanzado')
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'El usuario no existe')
        ]
    )]
    public function __invoke(HabilidadReadFromUserService $readService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario){
            return new JsonResponse(['message' => 'El usuario no existe', 'status' => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }
        return $readService($usuario);
    }
}