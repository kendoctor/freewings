<?php

namespace App\Repository;

use App\Entity\WallPaintingArtist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WallPaintingArtist|null find($id, $lockMode = null, $lockVersion = null)
 * @method WallPaintingArtist|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallPaintingArtist[]    findAll()
 * @method WallPaintingArtist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WallPaintingArtistRepository extends ServiceEntityRepository
{
    private $userRepository;

    public function __construct(RegistryInterface $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, WallPaintingArtist::class);
        $this->userRepository = $userRepository;
    }

    public function getAvailableArtists($wallPaintingId)
    {
        $artists = $this->userRepository->getArtists();

        $wallPaintingArtists = [];

        if($wallPaintingArtists !== null) {
            $wallPaintingArtists = $this->createQueryBuilder('wa')
                ->addSelect('a')
                ->leftJoin('wa.artist', 'a')
                ->leftJoin('wa.wallPainting', 'w')
                ->where('w.id = :wallPaintingId')
                ->setParameter('wallPaintingId', $wallPaintingId)
                ->getQuery()
                ->getResult()
            ;
        }


        /** @var WallPaintingArtist $wallPaintingArtist */
        foreach ($wallPaintingArtists as $wallPaintingArtist) {
            $artistId =  $wallPaintingArtist->getArtist()->getId();
            if (isset($artists[$artistId])) {
                unset($artists[$artistId]);
            }
        }

        $unassociatedWallPaintingArtists = [];
        foreach ($artists as $artist)
        {
            $wallPaintingArtist = new WallPaintingArtist();
            $wallPaintingArtist->setArtist($artist);
            $unassociatedWallPaintingArtists[] = $wallPaintingArtist;
        }

        $ret =  array_merge($wallPaintingArtists, $unassociatedWallPaintingArtists);

        return $ret;
    }

//    /**
//     * @return WallPaintingArtist[] Returns an array of WallPaintingArtist objects
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
    public function findOneBySomeField($value): ?WallPaintingArtist
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
