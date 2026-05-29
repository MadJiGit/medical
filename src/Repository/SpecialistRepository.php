<?php

namespace App\Repository;

use App\Entity\Specialist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SpecialistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specialist::class);
    }

    /** @return Specialist[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = true')
            ->orderBy('s.sortOrder', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
