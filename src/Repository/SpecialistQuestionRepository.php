<?php

namespace App\Repository;

use App\Entity\SpecialistQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SpecialistQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialistQuestion::class);
    }

    /** @return SpecialistQuestion[] */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.isPublic = true')
            ->orderBy('q.answeredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
