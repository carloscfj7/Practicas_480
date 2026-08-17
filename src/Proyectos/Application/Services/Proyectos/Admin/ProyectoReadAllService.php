<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Proyectos\Admin;

use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadAllService
{
    public function __construct(private ProyectoRepositoryInterface $proyectoRepository, private ProyectoDto $proyectoDto)
    {
    }


    public function __invoke():JsonResponse
    {
        $proyectos = $this->proyectoRepository->getAll();
        if ($proyectos === [])
        {
            return new JsonResponse(['message' => 'No hay ningun proyecto'], Response::HTTP_OK);
        }
        $result = $this-> proyectoDto->collectionFromEntities($proyectos);
        return new JsonResponse(['message'=>'Estos todos los proyectos' ,"proyectos" => $result], Response::HTTP_OK);
    }

}
