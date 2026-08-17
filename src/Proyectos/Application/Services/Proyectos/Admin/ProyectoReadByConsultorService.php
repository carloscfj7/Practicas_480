<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Admin;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadByConsultorService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ConsultorRepositoryInterface $consultorRepository, private ProyectoDto $proyectoDto)
    {
    }

    public function __invoke(array $data):JsonResponse
    {
        $this->validateRequieredData($data);
        $consultor = $this->consultorRepository->validateConsultor($data['email']);
        $proyectos = $this->proyectoRepository->getProyectosByConsultor($consultor);
        if ($proyectos === []){
            return new JsonResponse(['message' => 'El consultor ' . $data['email'] . ' no tiene ningun proyecto'], Response::HTTP_OK);
        }
        $result = $this->proyectoDto->collectionFromEntities($proyectos);
        return new JsonResponse(['message'=>'Estos todos los proyectos del consultor con email: '.$data['email'],"proyectos" => $result], Response::HTTP_OK);

    }

    private function validateRequieredData(array $data): void
    {
        if (empty($data['email'])) {
            throw new RequiredDataException();
        }
    }

}