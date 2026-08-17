<?php

declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Repositories;

use App\Proyectos\Application\Exceptions\Actividad\ActividadNotFoundException;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Exceptions\Actividad\ExistentActividadException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ValueObjects\ActividadId;
use App\Usuarios\Domain\Usuario;
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
class ActividadRepository extends ServiceEntityRepository  implements ActividadRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Actividad::class);
    }

    public function save(Actividad $actividad): void
    {
        $this->entityManager->persist($actividad);
        $this->entityManager->flush();
    }

    public function findById(ActividadId $id): ?Actividad
    {
        return $this->entityManager->getRepository(Actividad::class)->findOneBy(['id' => $id]);
    }

    public function findByNombreAndProyecto(string $nombre, Proyecto $proyecto): ?Actividad
    {
        return $this->entityManager->getRepository(Actividad::class)->findOneBy(['nombre' => $nombre, 'proyecto' => $proyecto]);
    }

    public function remove(Actividad $actividad): void
    {
        $this->entityManager->remove($actividad);
        $this->entityManager->flush();
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Actividad::class)->findAll();
    }
    public function findByNombreProyectoAndUsuario(string $nombre,Proyecto $proyecto, Usuario $usuario): ?Actividad
    {
        return $this->entityManager->getRepository(Actividad::class)->findOneBy(['nombre' => $nombre, 'proyecto' => $proyecto, 'usuario' => $usuario]);
    }

    public function findByProyecto(Proyecto $proyecto): array
    {
        return $this->entityManager->getRepository(Actividad::class)->findBy(['proyecto' => $proyecto]);
    }

    public function findByUsuario(Usuario $usuario): array
    {
        return $this->entityManager->getRepository(Actividad::class)->findBy(['usuario' => $usuario->getId()]);
    }

    public function validateActividadByNombreProyectoAndUsuario(string $nombre, Proyecto $proyecto, Usuario $usuario):Actividad{
        $actividad = $this->findByNombreProyectoAndUsuario($nombre, $proyecto, $usuario);
        if (!$actividad instanceof \App\Proyectos\Domain\Actividad){
            throw new ActividadNotFoundException("La actividad no existe o no esta disponible para el consultor autenticado actualmente");
        }
        return $actividad;
    }

    public function validateActividadByNombreAndProyecto(string $nombre, Proyecto $proyecto):Actividad{
        $actividad = $this->findByNombreAndProyecto($nombre, $proyecto);
        if (!$actividad instanceof \App\Proyectos\Domain\Actividad){
            throw new ActividadNotFoundException("No existe ninguna actividad con ese nombre en el proyecto ". $proyecto->getNombre());
        }
        return $actividad;
    }
    public function validateExistentActividad(string $nombre, Proyecto $proyecto, Usuario $usuario): void{
        $actividad = $this->findByNombreProyectoAndUsuario($nombre, $proyecto, $usuario);
        if ($actividad instanceof \App\Proyectos\Domain\Actividad){
            throw  new ExistentActividadException();
        }
    }
}