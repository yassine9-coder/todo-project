<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    // Add custom methods here

    /**
     * @return Tache[] Returns an array of Tache objects
     */
    public function findAllTasks(): array
    {
        return $this->findAll();
    }

    /**
     * @param int $userId
     * @return Tache[] Returns an array of Tache objects for a specific user
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // You can add more custom methods as needed
}
