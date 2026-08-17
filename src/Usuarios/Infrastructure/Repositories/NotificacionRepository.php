<?php

declare(strict_types=1);

namespace App\Usuarios\Infrastructure\Repositories;

use App\Usuarios\Application\Exceptions\Notificacion\NotificacionNotFoundException;
use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\NotificacionRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Notificacion>
 *
 * @implements PasswordUpgraderInterface<Notificacion>
 *
 * @method Notificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notificacion[]    findAll()
 * @method Notificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class NotificacionRepository extends ServiceEntityRepository implements NotificacionRepositoryInterface
{

    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Notificacion::class);
    }

    public function save(Notificacion $notificacion): void
    {
        $this->entityManager->persist($notificacion);
        $this->entityManager->flush();
    }

    public function delete(Notificacion $notificacion): void
    {
        $this->entityManager->remove($notificacion);
        $this->entityManager->flush();
    }



    public function getALl():array
    {
        return $this->entityManager->getRepository(Notificacion::class)->findAll();
    }

    public function findByCreador(Usuario $usuario): array
    {
        return $this->entityManager->getRepository(Notificacion::class)->findBy(['creador' => $usuario]);
    }

    public function findByUsuario(Usuario $usuario): array
    {
        $qb = $this->createQueryBuilder('n');

        $qb->where(':usuario MEMBER OF n.usuarios')
            ->setParameter('usuario', $usuario)
            ->orderBy('n.fecha', 'DESC');

        return $qb->getQuery()->getResult();

    }


    public function findById(NotificacionId $id): ?Notificacion
    {
        return $this->entityManager->getRepository(Notificacion::class)->find($id);

    }

    public function findByFecha(\DateTimeInterface $fecha): array
    {
        $startOfDay = \DateTimeImmutable::createFromInterface($fecha)->setTime(0, 0, 0);


        $endOfDay = $startOfDay->modify('+1 day');

        $qb = $this->createQueryBuilder('n');

        $qb->where('n.fecha >= :startOfDay')
        ->andWhere('n.fecha < :endOfDay')
        ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('n.fecha', 'ASC');
        return $qb->getQuery()->getResult();
    }
    public function findByFechaYCreador(\DateTimeInterface $fecha, Usuario $creador): array
    {
        $startOfDay = DateTimeImmutable::createFromInterface($fecha)->setTime(0, 0, 0);
        $endOfDay = $startOfDay->modify('+1 day');

        $qb = $this->createQueryBuilder('n');

        $qb->where('n.fecha >= :startOfDay')
        ->andWhere('n.fecha < :endOfDay')
        ->andWhere('n.creador = :creador')
        ->setParameter('startOfDay', $startOfDay)
        ->setParameter('endOfDay', $endOfDay)
        ->setParameter('creador', $creador)
        ->orderBy('n.fecha', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findByFechaYUsuario( \DateTimeInterface $fecha, Usuario $usuario,): array
    {
        $startOfDay = DateTimeImmutable::createFromInterface($fecha)->setTime(0, 0, 0);
        $endOfDay = $startOfDay->modify('+1 day');

        $qb = $this->createQueryBuilder('n');

        $qb->where('n.fecha >= :startOfDay')
        ->andWhere('n.fecha < :endOfDay')
        ->andWhere(':usuario MEMBER OF n.usuarios')
        ->setParameter('startOfDay', $startOfDay)
        ->setParameter('endOfDay', $endOfDay)
        ->setParameter('usuario', $usuario)
        ->orderBy('n.fecha', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function validateNotificacicon(NotificacionId $id):Notificacion{
        $notificacion = $this->findById($id);
        if (!$notificacion instanceof \App\Usuarios\Domain\Notificacion){
            throw new NotificacionNotFoundException();
        }
        return $notificacion;
    }
}