<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Commons\TokenAndRefreshTokenEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;

/**
 * @extends ServiceEntityRepository<TokenAndRefreshTokenEntity>
 */
class TokenAndRefreshTokenEntityRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenAndRefreshTokenEntity::class);
    }

    public function findInvalid($datetime = null)
    {
        if ($datetime === null) {
            $datetime = new \DateTime();
        }

        return $this->createQueryBuilder('rt')
            ->where('rt.valid < :datetime')
            ->setParameter('datetime', $datetime)
            ->getQuery()
            ->getResult();    }
}