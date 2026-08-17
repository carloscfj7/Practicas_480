<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente;

use App\Clientes\Application\Dto\DataRequest\ClienteUpdateRequestDto;
use App\Clientes\Application\Services\ClienteUpdateService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Usuarios\Domain\Usuario;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/cliente/update', name: 'update_cliente', methods: 'PUT')]
class ClienteUpdateController extends AbstractController
{
    #[OA\Put(
        path: "/api/cliente/update",
        description: "Permite que un cliente autenticado actualice su información.",
        summary: "Actualizar datos del cliente autenticado",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "contacto", type: "string", example: "876545678"),
                    new OA\Property(property: "direccion", type: "string", example: "Calle 2")
                ]
            )
        ),
        tags: ["Cliente"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cliente actualizado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Datos actualizados correctamente"),
                        new OA\Property(property: "contacto", type: "string", example: "876545678"),
                        new OA\Property(property: "direccion", type: "string", example: "calle 2"),

                    ]
                )
            ),
            new OA\Response(response: 401, description: "El usuario no está autenticado"),
            new OA\Response(response: 400, description: "Faltan datos obligatorios"),
        ]
    )]
    public function __invoke(ClienteUpdateService $updateService, Request $request):JsonResponse
    {

        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario) {
            return new JsonResponse(['error' => 'El usuario no está autenticado.'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $data = new ClienteUpdateRequestDto($data['contacto'], $data['direccion']);
        $response =  $updateService($usuario,$data);

        return new JsonResponse(['message' => $response->message, 'contacto' => $response->actualizacion['contacto'] ?? null, 'direccion' => $response->actualizacion['direccion'] ?? null], Response::HTTP_OK);
    }
}