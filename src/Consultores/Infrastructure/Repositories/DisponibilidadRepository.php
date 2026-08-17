<?php

declare(strict_types=1);

namespace App\Consultores\Infrastructure\Repositories;


use App\Consultores\Application\Exceptions\Disponibilidad\DisponibilidadNotFoundException;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Consultores\Domain\Exceptions\Disponibilidad\ExistentDisponibilidadException;
use App\Consultores\Domain\Habilidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Habilidad>
 *
 * @method Disponibilidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disponibilidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disponibilidad[] findALl()
 * @method Disponibilidad[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibilidadRepository extends ServiceEntityRepository implements DisponibilidadRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilidad::class);
    }

    public function save(Disponibilidad $disponibilidad): void
    {
        $this->entityManager->persist($disponibilidad);
        $this->entityManager->flush();
    }

    public function remove(Disponibilidad $disponibilidad): void
    {
        $this->entityManager->remove($disponibilidad);
        $this->entityManager->flush();
    }

    public function getAll(): array
    {
        return $this->findALl();
    }

    public function findByConsultor(Consultor $consultor): array
    {
        return $this->findBy(['consultor' => $consultor]);
    }

    public function findByConsultorAndInicio(Consultor $consultor, \DateTimeInterface $inicio): ?Disponibilidad
    {
        return $this->findOneBy(['consultor' => $consultor, 'fecha_ini' => $inicio]);
    }


    public function validateDisponibilidad(Consultor $consultor, \DateTime $fecha_ini): Disponibilidad
    {
        $disponibilidad = $this->findByConsultorAndInicio($consultor, $fecha_ini);
        if (!$disponibilidad instanceof \App\Consultores\Domain\Disponibilidad) {
            throw new DisponibilidadNotFoundException();
        }
        return $disponibilidad;
    }

    public function validateExistentDisponibilidad(Consultor $consultor, \DateTime $fecha_ini): void
    {
        $disponibilidad = $this->findByConsultorAndInicio($consultor, $fecha_ini);
        if ($disponibilidad instanceof \App\Consultores\Domain\Disponibilidad) {
            throw new ExistentDisponibilidadException();
        }
    }


}