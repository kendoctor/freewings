<?php

namespace App\Repository;

use App\Entity\UserTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTranslation[]    findAll()
 * @method UserTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTranslationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserTranslation::class);
    }

//    /**
//     * @return UserTranslation[] Returns an array of UserTranslation objects
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
    public function findOneBySomeField($value): ?UserTranslation
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
