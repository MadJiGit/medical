<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity = null, bool $flush = false): void
    {
        if ($entity) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get all active categories
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.nameBg', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->createQueryBuilder('c')
            ->where('c.slug = :slug')
            ->andWhere('c.isActive = :active')
            ->setParameter('slug', $slug)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
