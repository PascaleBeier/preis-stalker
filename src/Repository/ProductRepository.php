<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param string $link
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByLink(string $link)
    {
        return $this->createQueryBuilder('p')
            ->where('p.link = :link')->setParameter('link', $link)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param $id
     * @param $price
     *
     * @return array
     */
    public function updatePrice($id, $price)
    {
        return $this
            ->createQueryBuilder('p')
            ->update('App:Product', 'p')
            ->set('p.price', '?1')->setParameter(1, $price)
            ->where('p.id = ?2')->setParameter(2, $id)
            ->getQuery()
            ->getScalarResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.something = :value')->setParameter('value', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
