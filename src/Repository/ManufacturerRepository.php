<?php

namespace App\Repository;

use App\Entity\Manufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manufacturer>
 */
class ManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manufacturer::class);
    }

    public function findOrCreateByName(string $name): Manufacturer
    {
        $slug = $this->generateSlug($name);

        $manufacturer = $this->findOneBy(['slug' => $slug]);

        if (!$manufacturer) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($name);
            $manufacturer->setSlug($slug);

            $this->getEntityManager()->persist($manufacturer);
            $this->getEntityManager()->flush();
        }

        return $manufacturer;
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}
