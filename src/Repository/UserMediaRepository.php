<?php

namespace App\Repository;

use App\Entity\UserMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMedia[]    findAll()
 * @method UserMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMediaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserMedia::class);
    }

//    /**
//     * @return UserMedia[] Returns an array of UserMedia objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMedia
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
