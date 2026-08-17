<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Consultor;

use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadReadAllConsultorService;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route ('/disponibilidad/read/all', name: 'readAll_disponibilidad', methods: ['GET'])]
class DisponibilidadReadAllConsultorController extends AbstractController
{
    #[OA\Get(
        path: '/api/disponibilidad/read/all',
        summary: 'Leer todas las disponibildiades del consultor autenticado',
        tags: ['Disponibilidad'],
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
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(DisponibilidadReadAllConsultorService $readService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['message' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }

        $response = $readService($usuario);
        if($response === null){
            return new JsonResponse(['message' => 'No existen disponibilidades para el consultor con email: '.$usuario->getEmail()->value()], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse(['message' => 'Estas son las disponibilidades del consultor: ', 'disponibilidad' => $response], Response::HTTP_OK);
    }
}