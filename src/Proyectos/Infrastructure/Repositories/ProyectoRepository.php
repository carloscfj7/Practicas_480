<?php
declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Repositories;

use App\Clientes\Domain\Cliente;
use App\Consultores\Domain\Consultor;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Domain\Exceptions\Proyecto\ExistentProyectoException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\ProyectoId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Proyecto>
 *
 * @method Proyecto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proyecto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proyecto[] findALl ()
 * @method Proyecto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProyectoRepository extends ServiceEntityRepository  implements ProyectoRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Proyecto::class);
    }

    public function save(Proyecto $proyecto): void
    {
        $this->entityManager->persist($proyecto);
        $this->entityManager->flush();
    }

    public function findById(ProyectoId $id): ?Proyecto
    {
        return $this->entityManager->getRepository(Proyecto::class)->findOneBy(['id' => $id]);
    }

    public function findByNombre(string $nombre): ?Proyecto
    {
        return $this->entityManager->getRepository(Proyecto::class)->findOneBy(['nombre' => $nombre]);
    }

    public function remove(Proyecto $proyecto): void
    {
        $this->entityManager->remove($proyecto);
        $this->entityManager->flush();
    }


    public function getProyectosByCliente(Cliente $cliente): array
    {
        return $this->entityManager->getRepository(Proyecto::class)->findBy(['cliente' => $cliente->getId()]);
    }

    public function getProyectosByConsultor(Consultor $consultor): array
    {
        return $consultor->getProyectos()->toArray();
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Proyecto::class)->findAll();
    }

    public function getProyectoByClienteAndNombre(Cliente $cliente, string $nombre): ?Proyecto
    {
        return $this->entityManager->getRepository(Proyecto::class)->findOneBy(['cliente' => $cliente, 'nombre' => $nombre]);
    }
    public function getProyectoByConsultorAndNombre(Consultor $consultor, string $nombre): ?Proyecto
    {
        $proyectos = $this->getProyectosByConsultor($consultor);
        $proyecto = array_filter($proyectos, function ($proyecto) use ($nombre) {
            return $proyecto->getNombre() === $nombre;
        });
        return $proyecto === [] ? null : array_values($proyecto)[0];
    }

    public function getConsultoresFromProyecto(Proyecto $proyecto): array
    {
        return $proyecto->getConsultores()->toArray();
    }

    public function removeConsultorFromProyectoByEmail(Proyecto $proyecto, Consultor $consultor): void
    {
        $proyecto->removeConsultor($consultor);
    }

    public function addConsultorToProyecto(Proyecto $proyecto, Consultor $consultor): void
    {
        $proyecto->addConsultor($consultor);
    }

    public function validateProyectoByNombreAndConsultor(string $nombre, Consultor $consultor):Proyecto{
        $proyecto = $this->getProyectoByConsultorAndNombre($consultor, $nombre);
        if (!$proyecto instanceof \App\Proyectos\Domain\Proyecto){
            throw new ProyectoNotFoundException("El proyecto no existe o no esta disponible para el consultor autenticado actualmente");
        }
        return $proyecto;
    }

    public function validateProyectoByNombreAndCliente(string $nombre, Cliente $cliente):Proyecto
    {
        $proyecto = $this->getProyectoByClienteAndNombre($cliente, $nombre);
        if (!$proyecto instanceof \App\Proyectos\Domain\Proyecto){
            throw new ProyectoNotFoundException("El proyecto no existe o no esta disponible para el cliente autenticado actualmente");
        }
        return $proyecto;
    }

    public function validateProyectoByNombre(string $nombre):Proyecto{
        $proyecto = $this->findByNombre($nombre);
        if (!$proyecto instanceof \App\Proyectos\Domain\Proyecto){
            throw new ProyectoNotFoundException();
        }
        return $proyecto;
    }

    public function validateExistentProyecto(string $nombre): void
    {
        if ($this->findByNombre($nombre) instanceof \App\Proyectos\Domain\Proyecto) {
            throw new ExistentProyectoException();
        }
    }

}