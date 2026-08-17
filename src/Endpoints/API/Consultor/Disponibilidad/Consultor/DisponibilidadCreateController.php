<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadCreateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/disponibilidad/create', name: 'create_disponibilidad', methods: ['POST'])]
class DisponibilidadCreateController extends AbstractController
{
    #[OA\Post(
        path: '/api/disponibilidad/create',
        summary: 'Crear una nueva disponibilidad para un consultor',
        requestBody: new OA\RequestBody(
            description: 'Datos para crear la disponibilidad del consultor',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'fecha_ini', type: 'string', format: 'date-time', example: '2025-04-24 15:30:00'),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', example: '2025-05-14 15:30:00'),
                    new OA\Property(property: 'disponible', type: 'boolean', example: true)
                ],
                type: 'object'
            )
        ),

        tags: ['Disponibilidad'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Disponibilidad creada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Disponibilidad creada correctamente'),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Faltan datos obligatorios'),
            new OA\Response(response: 404, description: 'El cocnsultor no existe')
        ]
    )]
    public function  __invoke(Request $request, DisponibilidadCreateService $createService):JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario){
            return new JsonResponse(['error' => 'El usuario no existe', 'status' => Response::HTTP_NOT_FOUND]);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->valdiateRequiredData($data);
        $data = new DisponibilidadConsultorRequestDto($data['fecha_ini'], $data['fecha_fin'], $data['disponible']);
        $response = $createService($usuario, $data);
        return new JsonResponse(['message' => $response->message, 'email' => $response->email], Response::HTTP_OK);
    }

    private function valdiateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['disponible']) || empty($data['fecha_ini']) || empty($data['fecha_fin'])) {
            throw new RequiredDataException();
        }
        return null;
    }

}