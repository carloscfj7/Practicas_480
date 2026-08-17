<?php

namespace App\Consultores\Application\Services\Consultor\Admin;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadByEmailService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository, private ConsultorDto $consultorDto)
    {
    }

    public function __invoke(array $data): ?ConsultorDto
    {
        $consultor = $this->consultorRepository->validateConsultor($data['email']);
        return $this->consultorDto->fromEntity($consultor);

    }


}