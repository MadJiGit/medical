<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity = null, bool $flush = false): void
    {
        if ($entity) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get all active products
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.nameBg', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by category
     */
    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('p.nameBg', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.isActive = :active')
            ->setParameter('slug', $slug)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get products with low stock (quantity <= threshold)
     */
    public function findLowStock(int $threshold = 10): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.quantity <= :threshold')
            ->andWhere('p.isActive = :active')
            ->setParameter('threshold', $threshold)
            ->setParameter('active', true)
            ->orderBy('p.quantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get products grouped by category
     */
    public function findGroupedByCategory(): array
    {
        $products = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->where('p.isActive = :active')
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.nameBg', 'ASC')
            ->addOrderBy('p.nameBg', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($products as $product) {
            $categoryId = $product->getCategory()->getId();
            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [
                    'category' => $product->getCategory(),
                    'products' => []
                ];
            }
            $grouped[$categoryId]['products'][] = $product;
        }

        return array_values($grouped);
    }
}
