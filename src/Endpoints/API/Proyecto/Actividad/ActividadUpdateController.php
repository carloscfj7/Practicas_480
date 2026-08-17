<?php

declare(strict_types=1);

namespace App\Endpoints\API\Proyecto\Actividad;

use App\Proyectos\Application\Services\Actividad\ActividadUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/actividad/update', name: 'update_actividad', methods: ['PUT'])]
class ActividadUpdateController extends AbstractController
{
    #[OA\Put(
        path: '/api/actividad/update',
        summary: 'Actualizar una actividad',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['proyecto', 'nombre'],
                properties: [
                    new OA\Property(property: 'proyecto', type: 'string', example: "Proyecto 2"),
                    new OA\Property(property: 'nombre', type: 'string', example: 'Actividad 1'),
                    new OA\Property(property: 'descripcion', type: 'string', example: 'Nueva descripcion de la actividad')
                ],
                type: 'object'
            )
        ),
        tags: ["Actividad"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividad actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Nueva descripcion de la actividad'),
                        new OA\Property(property: 'actualizado', type: 'string', example: "descripcion 1")
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Faltan datos obligatorios',),
            new OA\Response(response: 404, description: 'La actividad no existe')
        ]
    )]
    public function __invoke(Request $request, ActividadUpdateService $updateService)
    {
       $usuario = $this->getUser();
       if (!$usuario instanceof Usuario) {
           return $this->json(['error' => 'No se ha encontrado el usuario'], Response::HTTP_UNAUTHORIZED);
       }
       $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
       return $updateService($data, $usuario);
    }
}