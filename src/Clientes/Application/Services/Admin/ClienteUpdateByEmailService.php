<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services\Admin;

use App\Clientes\Application\Dto\DataRequest\Admin\ClienteUpdateAdminRequestDto;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Dto\Response\UpdateServicesResponseDto;

class ClienteUpdateByEmailService
{
    public function __construct(private ClienteRepositoryInterface $clienteRepository)
    {
    }

    public function __invoke(ClienteUpdateAdminRequestDto $data): UpdateServicesResponseDto
    {
        $cliente = $this->clienteRepository->validateClienteOrFails($data->email);

        $actualizado = [];

        if ($data->contacto !== null && $data->contacto !== '' && $data->contacto !== '0' && $data->contacto !== $cliente->getContacto()) {
            $cliente->setContacto($data->contacto);
            $actualizado['contacto'] = $data->contacto;
        }

        if ($data->direccion !== null && $data->direccion !== '' && $data->direccion !== '0' && $data->direccion !== $cliente->getDireccion()) {
            $cliente->setDireccion($data->direccion);
            $actualizado['direccion'] = $data->direccion;
        }
        if ($actualizado === []) {
            return new UpdateServicesResponseDto("No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales");
        }

        $this->clienteRepository->save($cliente);
        return new UpdateServicesResponseDto(
            "Datos actualizados correctamente",
            array_filter($actualizado, fn($v) => $v !== null)
        );
    }


}
