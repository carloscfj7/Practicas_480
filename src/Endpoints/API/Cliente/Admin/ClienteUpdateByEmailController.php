<?php

declare(strict_types=1);

namespace App\Endpoints\API\Cliente\Admin;

use App\Clientes\Application\Dto\DataRequest\Admin\ClienteUpdateAdminRequestDto;
use App\Clientes\Application\Dto\DataRequest\ClienteUpdateRequestDto;
use App\Clientes\Application\Services\Admin\ClienteUpdateByEmailService;
use App\Shared\Application\Exceptions\NoJsonProvidedException;
use App\Shared\Application\Exceptions\RequiredDataException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/cliente/update', name: 'upadate_cliente_admin', methods: ['PUT'])]
class ClienteUpdateByEmailController extends AbstractController
{


    #[OA\Put(
        path: "/api/admin/cliente/update",
        description: "Permite a un administrador actualizar los datos de un cliente utilizando su email.",
        summary: "Actualizar cliente por email (admin)",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "cliente@ejemplo.com"),
                    new OA\Property(property: "contacto", type: "string", example: "267483928"),
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
                        new OA\Property(property: "contacto", type: "string", example: "267483928"),
                        new OA\Property(property: "direccion", type: "string", example: "calle 2"),

                    ]
                )
            ),
            new OA\Response(response: 404, description: "El cliente o el usuario no existen "),
            new OA\Response(response: 400, description: "Faltan datos obligatorios")


]
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(ClienteUpdateByEmailService $updateService , Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new NoJsonProvidedException();
        }
        $this->validateRequiredData($data);
        $data = new ClienteUpdateAdminRequestDto($data['email'], $data['contacto'], $data['direccion']);
        $response =  $updateService($data);

        return new JsonResponse(['message' => $response->message, 'contacto' => $response->actualizacion['contacto'] ?? null, 'direccion' => $response->actualizacion['direccion'] ?? null], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data){
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }

}