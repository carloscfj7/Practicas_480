<?php
declare(strict_types=1);

namespace App\Clientes\Application\Services;

use App\Clientes\Application\Dto\DataRequest\ClienteUpdateRequestDto;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Dto\Response\UpdateServicesResponseDto;
use App\Usuarios\Domain\Usuario;


final readonly class ClienteUpdateService
{

    public function __construct(private ClienteRepositoryInterface $clienteRepository)
    {
    }

    public function __invoke(Usuario $usuario, ClienteUpdateRequestDto $data): UpdateServicesResponseDto
    {
        $cliente = $this->clienteRepository->validateClienteOrFails($usuario->getEmail()->value());

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