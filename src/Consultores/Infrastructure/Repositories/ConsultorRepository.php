<?php
declare(strict_types=1);

namespace App\Consultores\Infrastructure\Repositories;


use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Exceptions\Consultor\ConsultorWithProjectsException;
use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Proyectos\Domain\Proyecto;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;

/**
 * @extends ServiceEntityRepository<Consultor>
 *
 * @method Consultor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consultor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consultor[] findALl()
 * @method Consultor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultorRepository extends ServiceEntityRepository implements ConsultorRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultor::class);
    }
    public function save(Consultor $consultor): void
    {
        $this->entityManager->persist($consultor);
        $this->entityManager->flush();
    }

    public function findById(ConsultorId $id): ?Consultor
    {
        return $this->entityManager->getRepository(Consultor::class)->findOneBy(['id' => $id]);

    }

    public function findByEmailUsuario(string $email): ?Consultor
    {
        $usuario = $this->entityManager->getRepository(Usuario::class)->findOneBy(['email' => $email]);
        if ($usuario){
            $usuarioId = $usuario->getId();
            return $this->entityManager->getRepository(Consultor::class)->findOneBy(['usuario' => $usuarioId]);
        }
        return null;
    }
    public function remove(Consultor $consultor): void
    {
        $this->entityManager->remove($consultor);
        $this->entityManager->flush();
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Consultor::class)->findAll();
    }

    public function validateConsultor(string $email): Consultor
    {
        $consultor = $this->findByEmailUsuario($email);
        if (!$consultor instanceof \App\Consultores\Domain\Consultor) {
            throw new ConsultorNotFoundException();
        }
        return $consultor;
    }


    public function addConsultoresToProyecto(Proyecto $proyecto, array $consultores): bool
    {
        foreach ($consultores as $email_consultor) {
            $consultor = $this->findByEmailUsuario($email_consultor);
            if (!$consultor instanceof \App\Consultores\Domain\Consultor) {
                throw new ConsultorNotFoundException("El consultor " . $email_consultor . " no existe");
            }
            $proyecto->addConsultor($consultor);
        }
        return true;
    }


}