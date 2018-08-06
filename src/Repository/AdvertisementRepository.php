<?php

namespace App\Repository;

use App\Entity\Advertisement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Advertisement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertisement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertisement[]    findAll()
 * @method Advertisement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }

    public function getListQuery()
    {
        return $this->createQueryBuilder('ad')
            ->getQuery();
    }

    public function create()
    {
        $entity = new Advertisement();

        return $entity;
    }

    public function persist($entity)
    {
        $this->_em->persist($entity);

    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getByOldId($oldId)
    {
        return $this->createQueryBuilder('ad')
            ->where('ad.oldId = :oldId')
            ->setParameter('oldId', $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getPublished($limit = 6)
    {
        $qb = $this->createQueryBuilder('ad')
            ->where('ad.isPublished = true')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Advertisement[] Returns an array of Advertisement objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advertisement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
