<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Habilidad;

use App\Consultores\Application\Dto\Entity\HabilidadDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HabilidadReadByConsultorService
{
    public function __construct(private HabilidadRepositoryInterface $habilidadRepository, private ConsultorRepositoryInterface $consultorRepository, private HabilidadDto $habilidadDto)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequiredData($data);
        $consultor  = $this->consultorRepository->validateConsultor($data['consultor']);
        $habilidades = $this->habilidadRepository->getHabilidadesByConsultor($consultor);
        if ($habilidades === []){
            return new JsonResponse(['message' => 'El consultor con email: '.$data['consultor'] . ' no tiene habilidades'], Response::HTTP_OK);
        }
        $habilidades = $this->habilidadDto->collectionFromEntities($habilidades);
        return new JsonResponse(['message' => 'Estas son las habilidades del consultor con email: '.$data['consultor'] ,'habilidades' => $habilidades], Response::HTTP_OK);
    }

    private function validateRequiredData(array $data): ? JsonResponse
    {
        if (empty($data['consultor'])) {
            throw new RequiredDataException();
        }
        return null;
    }

}