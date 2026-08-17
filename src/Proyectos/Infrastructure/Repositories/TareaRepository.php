<?php
declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Repositories;

use App\Consultores\Domain\Consultor;
use App\Proyectos\Application\Exceptions\Tarea\TareaNotFoundException;
use App\Proyectos\Domain\Exceptions\Tarea\ExistentTareaException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\TareaId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tarea>
 *
 * @method Tarea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tarea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tarea[] findALl ()
 * @method Tarea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TareaRepository extends ServiceEntityRepository implements TareaRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Tarea::class);
    }

    public function save(Tarea $tarea): void
    {
        $this->entityManager->persist($tarea);
        $this->entityManager->flush();
    }

    public function findById(TareaId $id): ?Tarea
    {
        return $this->entityManager->getRepository(Tarea::class)->findOneBy(['id' => $id]);

    }

    public function findByNombre(string $nombre): ?Tarea
    {
        return $this->entityManager->getRepository(Tarea::class)->findOneBy(['nombre' => $nombre]);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Tarea::class)->findAll();
    }

    public function getTareasByProyecto(Proyecto $proyecto): array
    {
        return $this->entityManager->getRepository(Tarea::class)->findBy(['proyecto' => $proyecto]);
    }

    public function remove(Tarea $tarea): void
    {
        $this->entityManager->remove($tarea);
        $this->entityManager->flush();

    }

    public function getTareasByConsultor(Consultor $consultor): array
    {
        return $consultor->getTareas()->toArray();
    }

    public function getTareasByProyectoAndConsultor(Proyecto $proyecto, Consultor $consultor): ?array
    {
        $tareas = $this->getTareasByConsultor($consultor);
        if ($tareas === []) {
            return [];
        }
        $tarea = array_filter($tareas, function ($tarea) use ($proyecto) {
            return $tarea->getProyecto() === $proyecto;
        });
        return $tarea === [] ? null : array_values($tarea);
    }

    public function getConsultoresByTarea(Tarea $tarea): array
    {
        return $tarea->getConsultores()->toArray();
    }

    public function getProyectoByTarea(Tarea $tarea): ?Proyecto
    {
        return $tarea->getProyecto();
    }

    public function getTareaByConsultorAndName(Consultor $consultor, string $nombre): ?Tarea
    {
        $tareas = $this->getTareasByConsultor($consultor);
        if ($tareas === []) {
            return null;
        }
        $tarea = array_filter($tareas, function ($tarea) use ($nombre) {
            return $tarea->getNombre() === $nombre;
        });
        return $tarea === [] ? null : array_values($tarea)[0];
    }

    public function getTareaByProyectoAndName(Proyecto $proyecto, string $nombre): ?Tarea
    {
        $tareas = $this->getTareasByProyecto($proyecto);
        $tarea = array_filter($tareas, function ($tarea) use ($nombre) {
            return $tarea->getNombre() === $nombre;
        });
        return $tarea === [] ? null : array_values($tarea)[0];
    }


    public function getTareaByConsultorAndProyectoAndName(Consultor $consultor, Proyecto $proyecto, string $nombre): ?Tarea
    {
        $tareas = $this->getTareasByProyectoAndConsultor($proyecto, $consultor);
        if ($tareas === null || $tareas === []) {
            return Null;
        }
        $tarea = array_filter($tareas, function ($tarea) use ($nombre) {
            return $tarea->getNombre() === $nombre;
        });
        return $tarea === [] ? null : array_values($tarea)[0];
    }

    public function getConsultoresFromTarea(Tarea $tarea): array
    {
        return $tarea->getConsultores()->toArray();
    }

    public function removeConsultorFromTareaByEmail(Tarea $tarea, Consultor $consultor): void
    {
        $tarea->removeConsultor($consultor);
    }

    public function addConsultorToTarea(Tarea $tarea, Consultor $consultor): void
    {
        $tarea->addConsultor($consultor);
    }

    public function validateTareaByProyectoAndNombre(string $nombre, Proyecto $proyecto): Tarea
    {
        $tarea = $this->getTareaByProyectoAndName($proyecto, $nombre);
        if (!$tarea instanceof \App\Proyectos\Domain\Tarea) {
            throw new TareaNotFoundException();
        }
        return $tarea;
    }

    public function validateTareaByConsultorAndNombre(string $nombre, Consultor $consultor): Tarea
    {
        $tarea = $this->getTareaByConsultorAndName($consultor, $nombre);
        if (!$tarea instanceof \App\Proyectos\Domain\Tarea) {
            throw new TareaNotFoundException();
        }
        return $tarea;
    }

    public function validateTareaByConsultorNombreAndProyecto(string $nombre, Consultor $consultor, Proyecto $proyecto): Tarea
    {
        $tarea = $this->getTareaByConsultorAndProyectoAndName($consultor, $proyecto, $nombre);
        if (!$tarea instanceof \App\Proyectos\Domain\Tarea) {
            throw new TareaNotFoundException();
        }
        return $tarea;
    }

    public function validateExistentTarea(string $nombre, Proyecto $proyecto): void
    {
        $tarea = $this->getTareaByProyectoAndName($proyecto, $nombre);
        if ($tarea instanceof \App\Proyectos\Domain\Tarea) {
            throw new ExistentTareaException();
        }
    }
}