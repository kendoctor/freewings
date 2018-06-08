<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function create()
    {
        $user = new User();
//
//        $tokens = ['icon', 'figure'];
//
//        foreach ($tokens as $token) {
//            $image = new UserMedia();
//            $image->setToken($token);
//            $user->addImage($image);
//        }

        return $user;
    }

    public function getCreatorByName($username = 'kendoctor')
    {

        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getArtists()
    {
        return $this->createQueryBuilder('u')
            ->where('BIT_AND(u.type, :type) > 0')
            ->setParameter('type', User::TYPE_ARTIST)
            ->indexBy('u', 'u.id')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
