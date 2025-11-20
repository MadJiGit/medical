<?php

namespace App\Repository;

use App\Entity\ContactRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactRequest>
 */
class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactRequest::class);
    }

    public function createContactRequest(User $user, string $message, ?string $productId = null): ContactRequest
    {
        $contactRequest = new ContactRequest();
        $contactRequest->setUser($user);
        $contactRequest->setMessage($message);
        $contactRequest->setProductId($productId);
        $contactRequest->setStatus('new');
        $contactRequest->setCreatedAt(new \DateTimeImmutable());

        $this->getEntityManager()->persist($contactRequest);

        return $contactRequest;
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}
