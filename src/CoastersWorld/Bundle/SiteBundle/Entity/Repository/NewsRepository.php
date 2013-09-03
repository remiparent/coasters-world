<?php

namespace CoastersWorld\Bundle\SiteBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
    public function findAllOrderedByDateDesc()
    {
        return $this
            ->createQueryBuilder('n')
            ->addOrderBy('n.publishedAt', 'DESC')
            ->getQuery()
        ;
    }

    public function findTwoLatest()
    {
        return $this
            ->createQueryBuilder('n')
            ->addOrderBy('n.publishedAt', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult()
        ;
    }
}