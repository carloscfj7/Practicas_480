<?php

namespace App\Consultores\Application\Services\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorUpdateRequestDto;
use App\Consultores\Application\Dto\Response\Consultor\ConsultorUpdateResponseDto;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Shared\Application\Dto\Response\UpdateServicesResponseDto;
use App\Usuarios\Domain\Usuario;

class ConsultorUpdateService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository, private HabilidadRepositoryInterface $habilidadRepository)
    {
    }

    public function __invoke(Usuario $usuario, ConsultorUpdateRequestDto $data): UpdateServicesResponseDto
    {
        $consultor = $this->consultorRepository->validateConsultor($usuario->getEmail()->value());
        $actualizado = [];

        if (!empty($data->perfil) && Perfil::fromString($data->perfil) !== $consultor->getPerfil()) {
            $actualizado['perfil'] =$data->perfil;
            $consultor->setPerfil(Perfil::fromString($data->perfil));
        }
        if (!empty($data->habilidades)){
            $consultor = $this->setHabilidades($data->habilidades, $consultor, $actualizado);
        }

        if (!empty($data->borrar_habilidades)){
            $consultor = $this->removeHabilidades($data->habilidades, $consultor, $actualizado);
        }

        if (!$actualizado){
            return new UpdateServicesResponseDto('No se ha actualizado ningún dato');
        }
        $this->consultorRepository->save($consultor);

        return new UpdateServicesResponseDto('Perfil actualizado correctamente', $actualizado);
    }

    private function setHabilidades(array $data, Consultor $consultor, &$actualizado): Consultor
    {
        $added = [];

        foreach ($data as $dataHabilidad) {
            $habilidad = $this->habilidadRepository->validateHabilidad($dataHabilidad);
            if (!in_array($habilidad, $this->habilidadRepository->getHabilidadesByConsultor($consultor), true))
            {
                $habilidad->addConsultor($consultor);
                $added[] = $dataHabilidad;
            }
        }
        if ($added !== []){
            $actualizado['habilidades'] = $added;
        }
        return $consultor;
    }

    private function removeHabilidades(array $data, Consultor $consultor, &$actualizado): Consultor
    {
        $removed = [];
        foreach ($data as $dataHabilidad) {

            $habilidad = $this->habilidadRepository->validateHabilidad($dataHabilidad);
            if (in_array($habilidad, $this->habilidadRepository->getHabilidadesByConsultor($consultor), true))
            {
                $habilidad->removeConsultor($consultor);
                $removed[] = $dataHabilidad;
            }
        }
        if ($removed !== []){
            $actualizado['removed_habilidades'] = $removed;
        }
        return $consultor;
    }



}