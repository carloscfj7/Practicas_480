<?php

namespace App\Consultores\Application\Services\Consultor\Admin;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadAllService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository, private ConsultorDto $consultorDto)
    {
    }


    public function __invoke():?array
    {
        $consultores = $this->consultorRepository->getAll();
        if ($consultores === []){
            return null;
        }
        return $this->consultorDto->collectionFromEntities($consultores);
    }

}