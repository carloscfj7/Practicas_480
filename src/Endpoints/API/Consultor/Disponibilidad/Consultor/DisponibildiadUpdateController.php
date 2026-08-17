<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route ('/disponibilidad/update', name: 'update_disponibilidad', methods: ['PUT'])]
class DisponibildiadUpdateController extends AbstractController
{
    #[OA\Put(
        path: '/api/disponibilidad/update',
        summary: 'Actualizar la disponibilidad del usuario a raiz del consultor autenticado',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ini'],
                properties: [
                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                    new OA\Property(property: 'disponible', type: 'boolean', example: false),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-04-25 15:30:00'),

                ],
                type: 'object'
            )
        ),
        tags: ['Disponibilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Disponibilidad actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'La disponibilidad se ha actualizado correctamente'),
                        new OA\Property(
                            property: 'actualizacion',
                            properties: [
                                new OA\Property(property: 'disponible', type: 'boolean', example: false),
                                new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-14 15:30:00'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request, DisponibilidadUpdateService $updateService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['message' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }

        $this->validateRequiredData($data);

        $data = new DisponibilidadConsultorRequestDto($data['fecha_ini'], $data['fecha_fin'], $data['disponible']);
        $response = $updateService($usuario, $data);
        return new JsonResponse(['message' => $response->message, 'actualizacion' => $response->actualizacion], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data)
    {
        if (empty($data['fecha_ini'])) {
            throw new RequiredDataException();
        }
    }




}