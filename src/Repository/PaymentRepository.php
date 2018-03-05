<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :uid AND p.done = TRUE')->setParameter('uid', $user->getId())
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
