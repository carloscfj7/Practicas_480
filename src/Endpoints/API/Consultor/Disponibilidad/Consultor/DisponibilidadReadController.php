<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Consultor;



use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadReadService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route ('/disponibilidad/read', name: 'read_disponibilidad', methods: ['GET'])]
class DisponibilidadReadController extends AbstractController
{
    #[OA\Get(
        path: '/api/disponibilidad/read',
        summary: 'Leer la disponibilidad del usuario autenticado a raiz de su fecha de inicio',
        tags: ['Disponibilidad'],
        parameters: [
            new OA\QueryParameter(
                name: "fecha_ini",
                description: "fecha de inicio de la disponibilidad",
                required: true,
                example: "2025-05-25 15:30:00",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Disponibilidad del usuario recuperada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'disponibilidad',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'disponible', type: 'boolean', example: true),
                                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-24 15:30:00'),
                                    new OA\Property(property: 'consultor', type: 'string', example: "consultor@prueba.es"),

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
    public function __invoke(Request $request, DisponibilidadReadService $readService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['message' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["fecha_ini" => $request->query->get('fecha_ini')];
        $this->valdiateRequiredData($data);
        $response = ($readService)($usuario, $data);
        return new JsonResponse(['message' => 'Esta es la disponibilidad buscada: ','disponibilidad' => $response], Response::HTTP_OK);
    }

    private function valdiateRequiredData(array $data):?JsonResponse
    {
        if (empty($data['fecha_ini'])) {
            throw new RequiredDataException();
        }
        return null;
    }
}