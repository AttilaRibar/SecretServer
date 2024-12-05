<?php

namespace App\Repository;

use App\Entity\Secret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Secret>
 */
class SecretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    /**
     * Returns a secret that found with the hash string.
     * It excludes the expired secrets and that's which don't have remaining views.
     *
     * @param string $hash Search hash
     *
     * @return Secret|null
     */
    public function findOneByHash(string $hash): ?Secret
    {
        $qb = $this->createQueryBuilder('s');
        return $qb
            ->andWhere('s.hash = :hash')
            ->andWhere('s.remainingViews > 0')
            ->andWhere($qb->expr()->orX('s.expiresAt > :now')->add('s.expiresAt IS NULL'))
            ->setParameter('hash', $hash)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

}
