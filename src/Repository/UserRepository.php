<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOrCreateClient(string $email, string $name, ?string $phone): User
    {
        $user = $this->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setName($name);
            $user->setPhone($phone);
            $user->setRoles(['ROLE_CLIENT']);
            $user->setIsActive(false);

            $this->getEntityManager()->persist($user);
        }

        return $user;
    }
}
