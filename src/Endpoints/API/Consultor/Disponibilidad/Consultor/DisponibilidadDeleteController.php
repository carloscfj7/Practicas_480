<?php

declare(strict_types=1);

namespace App\Endpoints\API\Consultor\Disponibilidad\Consultor;

use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibildiadDeleteService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/disponibilidad/delete', name: 'delete_disponibilidad', methods: ['DELETE'])]
class DisponibilidadDeleteController extends AbstractController
{
    #[OA\Delete(
        path: '/api/disponibilidad/delete',
        summary: 'Eliminar la disponibilidad de un consultor autenticado a raiz de la fehca de inicio de esta',
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
                description: 'Disponibilidad eliminada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Disponibilidad eliminada correctamente'),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'La disponibilidad no existe')
        ]
    )]
    public function __invoke(Request $request, DisponibildiadDeleteService $deleteService): JsonResponse
    {
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['message' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        $data = ["fecha_ini" => $request->query->get('fecha_ini')];
        $this->valdiateRequiredData($data);
        $response = $deleteService($usuario, $data);
        return new JsonResponse(['message' => $response], Response::HTTP_OK);

    }

    private function valdiateRequiredData(array $data):?JsonResponse
    {
        if (empty($data['fecha_ini'])) {
            throw new RequiredDataException();
        }
        return null;
    }
}