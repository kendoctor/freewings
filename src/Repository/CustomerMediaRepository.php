<?php

namespace App\Repository;

use App\Entity\CustomerMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomerMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerMedia[]    findAll()
 * @method CustomerMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerMediaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomerMedia::class);
    }

//    /**
//     * @return CustomerMedia[] Returns an array of CustomerMedia objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustomerMedia
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
