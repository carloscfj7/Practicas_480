<?php

namespace App\Clientes\Infrastructure\Repositories;

use App\Clientes\Application\Exceptions\ClienteNotFoundException;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Clientes\Domain\Exceptions\ClienteWithProjectsException;
use App\Clientes\Domain\ValueObjects\ClienteId;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use function PHPUnit\Framework\isEmpty;

/**
 * @extends ServiceEntityRepository<Cliente>
 *
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[] findALl ()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends ServiceEntityRepository implements ClienteRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    public function save(Cliente $consultor): void
    {
        $this->entityManager->persist($consultor);
        $this->entityManager->flush();
    }

    public function findById(ClienteId $id): ?Cliente
    {
        return $this->entityManager->getRepository(Cliente::class)->findOneBy(['id' => $id]);

    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByEmailUsuario(string $email): ?Cliente
    {
        return $this->entityManager
            ->getRepository(Cliente::class)
            ->createQueryBuilder('c')
            ->join('c.usuario', 'u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(Cliente $cliente): void
    {
        $this->entityManager->remove($cliente);
        $this->entityManager->flush();
    }

    public function getAll()
    {
        return $this->entityManager->getRepository(Cliente::class)->findAll();
    }

    /**
     * @throws ClienteNotFoundException
     */
    public function validateClienteOrFails(string $email): Cliente
    {
        $cliente = $this->findByEmailUsuario($email);
        if (!$cliente instanceof \App\Clientes\Domain\Cliente) {
            throw new ClienteNotFoundException();
        }
        return $cliente;
    }

    /**
     * @throws ClienteWithProjectsException
     */
    public function validateRemoveOrFail(Cliente $cliente)
    {
        $proyectos = $cliente->getProyectos();
        if(!isEmpty($proyectos)){
            throw new ClienteWithProjectsException();
        }
    }
}