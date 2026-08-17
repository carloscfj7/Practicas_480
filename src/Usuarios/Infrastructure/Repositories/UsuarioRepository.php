<?php
declare(strict_types=1);
namespace App\Usuarios\Infrastructure\Repositories;

use App\Shared\Domain\Model\UserId;
use App\Usuarios\Application\Exceptions\Usuario\UsuarioNotFoundException;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @implements PasswordUpgraderInterface<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository implements UsuarioRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function findByEmail(string $email): ?Usuario
    {
        return $this->entityManager->getRepository(Usuario::class)->findOneBy(['email' => $email]);
    }

    public function save(Usuario $usuario): void
    {
        $this->entityManager->persist($usuario);
        $this->entityManager->flush();
    }

    public function findById(UserId $id): ?Usuario
    {
        return $this->entityManager->getRepository(Usuario::class)->findOneBy(['id' => $id]);

    }

    public function remove(Usuario $usuario): void
    {
        $this->entityManager->remove($usuario);
        $this->entityManager->flush();
    }

    public function validateUsuario(string $email): Usuario{
        $usuario = $this->findByEmail($email);
        if (!$usuario instanceof \App\Usuarios\Domain\Usuario) {
            throw new UsuarioNotFoundException();
        }
        return $usuario;
    }}