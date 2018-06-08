<?php

namespace App\Repository;

use App\Entity\CategoryMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CategoryMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryMedia[]    findAll()
 * @method CategoryMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryMediaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CategoryMedia::class);
    }

//    /**
//     * @return CategoryMedia[] Returns an array of CategoryMedia objects
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
    public function findOneBySomeField($value): ?CategoryMedia
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
