<?php

declare(strict_types=1);

namespace App\Consultores\Application\Services\Disponibilidad\Admin;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadByConsultorAdminService
{

    public function __construct(private DisponibilidadRepositoryInterface $disponibilidadRepository, private ConsultorRepositoryInterface $consultorRepository, private DisponibilidadDto $disponibilidadDto)
    {
    }
    public function __invoke(array $data):JsonResponse
    {
        $this->valdiateRequiredData($data);


        $consultor = $this->consultorRepository->validateConsultor($data['consultor']);
        $disponibilidades = $this->disponibilidadRepository->findByConsultor($consultor);
        $disponibilidades = $this->disponibilidadDto->collectionFromEntities($disponibilidades);
        return new JsonResponse(['message' =>'Estas son las disponibilidades para el consultor: '.$data['consultor'] ,'disponibilidad' => $disponibilidades], Response::HTTP_OK);
    }

    private function valdiateRequiredData(array $data): ?JsonResponse
    {
        if (empty($data['consultor'])) {
            throw new RequiredDataException();
        }
        return null;
    }

}