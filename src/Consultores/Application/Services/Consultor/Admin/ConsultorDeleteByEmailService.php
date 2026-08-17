<?php

namespace App\Consultores\Application\Services\Consultor\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorDeleteByEmailService
{
    public function __construct(private ConsultorRepositoryInterface $consultorRepository)
    {
    }
    public function __invoke(array $data):string
    {
        $consultor = $this->consultorRepository->validateConsultor($data['email']);
        $this->consultorRepository->remove($consultor);
        return "Consultor eliminado correctamente";
    }


}