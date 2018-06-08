<?php

namespace App\Repository;

use App\Entity\WallPaintingPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WallPaintingPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method WallPaintingPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallPaintingPhoto[]    findAll()
 * @method WallPaintingPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WallPaintingPhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WallPaintingPhoto::class);
    }

//    /**
//     * @return WallPaintingPhoto[] Returns an array of WallPaintingPhoto objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WallPaintingPhoto
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
