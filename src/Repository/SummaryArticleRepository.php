<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\SummaryArticle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SummaryArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SummaryArticle::class);
    }


    public function findByUser($value)
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function getConfirmedSummaryArticlesByUser(User $user)
    {
        return $this->getConfirmedSummaryArticlesByUserQueryBuilder($user)
            ->getQuery()
            ->getResult();
    }

    public function getConfirmedSummaryArticlesByUserQueryBuilder(User $user)
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :userid AND s.state = :state')->setParameter('userid', $user->getId())->setParameter('state', 'Confirmed');
    }

    public function hasConfirmedSummary(User $user)
    {
        $q = $this->createQueryBuilder('s')
            ->leftJoin(Article::class, 'a', 'WITH', 's.id=a.summary')
            ->select('count(s.id) as count')
            ->where('s.user = :userid AND s.state = :state AND (s.article IS NULL)')->setParameter('userid', $user->getId())->setParameter('state', 'Confirmed')
            ->getQuery();
        return
            $q->getSingleResult()['count'] > 0;
    }
}
