<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\PostMedia;
use App\Entity\WallPainting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WallPainting|null find($id, $lockMode = null, $lockVersion = null)
 * @method WallPainting|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallPainting[]    findAll()
 * @method WallPainting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WallPaintingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WallPainting::class);
    }

    public function create()
    {
        $wallPainting = new WallPainting();

        $tokens = ['cover', 'thumb', 'list'];

        foreach ($tokens as $token) {
            $image = new PostMedia();
            $image->setMedia(new Media());
            $image->setToken($token);
            $wallPainting->addImage($image);
        }

        return $wallPainting;
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
        return $this->createQueryBuilder('w')
            ->where('w.oldId = :oldId')
            ->setParameter('oldId', $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getRecommended($limit  = 12)
    {
        return $this->createQueryBuilder('w')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;

    }


//    /**
//     * @return WallPainting[] Returns an array of WallPainting objects
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
    public function findOneBySomeField($value): ?WallPainting
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
