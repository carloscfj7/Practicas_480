<?php

declare(strict_types=1);

namespace App\Consultores\Infrastructure\Repositories;

use App\Consultores\Application\Exceptions\Habilidad\HabilidadNotFoundException;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Exceptions\Habilidad\ExistentHabilidadException;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Habilidad>
 *
 * @method Habilidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Habilidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Habilidad[] findALl()
 * @method Habilidad[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HabilidadRepository extends ServiceEntityRepository implements HabilidadRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Habilidad::class);
    }

    public function save(Habilidad $habilidad): void
    {
        $this->entityManager->persist($habilidad);
        $this->entityManager->flush();
    }

    public function remove(Habilidad $habilidad): void
    {
        $this->entityManager->remove($habilidad);
        $this->entityManager->flush();
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function findByNombreAndNivel(string $nombre, string $nivel): ?Habilidad
    {
        return $this->findOneBy(['nombre' => $nombre, 'nivel' => $nivel]);
    }

    public function getHabilidadesByConsultor(Consultor $consultor): array
    {
        return $consultor->getHabilidades()->toArray();
    }
    public function validateHabilidad(array $dataHabilidad): Habilidad{
        $habilidad = $this->findByNombreAndNivel($dataHabilidad['nombre'], $dataHabilidad['nivel']);
        if (!$habilidad instanceof \App\Consultores\Domain\Habilidad) {
            throw  new HabilidadNotFoundException();
        }
        return $habilidad;
    }

    public function setHabilidades(array $habilidades, Consultor $consultor): Consultor
    {
        foreach ($habilidades as $dataHabilidad) {
            $habilidad = $this->findByNombreAndNivel($dataHabilidad['nombre'], $dataHabilidad['nivel']);
            if (!$habilidad instanceof \App\Consultores\Domain\Habilidad) {
                throw new \Exception("La habilidad no existe ". $dataHabilidad['nombre']. $dataHabilidad['nivel']);
            }
            $habilidad->addConsultor($consultor);
        }
        return $consultor;
    }

    public function valdiateExistentHabilidad(Habilidad $habilidad){
        if ($this->findByNombreAndNivel($habilidad->getNombre(), $habilidad->getNivel()->value) instanceof \App\Consultores\Domain\Habilidad) {
            throw new ExistentHabilidadException();
        }
    }
}