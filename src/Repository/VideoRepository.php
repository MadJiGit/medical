<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('v.sortOrder', 'ASC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatest(int $limit = 3): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('v.sortOrder', 'ASC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findActiveByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.category = :cat')
            ->andWhere('v.isActive = :active')
            ->setParameter('cat', $categoryId)
            ->setParameter('active', true)
            ->orderBy('v.sortOrder', 'ASC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
