<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $userPasswordEncoder;

    public function __construct(RegistryInterface $registry, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
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

    public function getListQuery()
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ;
    }

    public function getCreatorByName($username = 'freewings')
    {

        $creator = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if(null === $creator)
        {
            $creator = $this->create();
            $creator->setUsername('freewings');
            $creator->setEmail('freewings@163.com');
            $creator->setPlainPassword('freewings');
            $password = $this->userPasswordEncoder->encodePassword($creator, $creator->getPlainPassword());
            $creator->setPassword($password);
            $this->_em->persist($creator);
            $this->_em->flush();
        }
        return $creator;
    }

    public function getArtistsRecommended($limit = 8)
    {
        return $this->createQueryBuilder('u')
            ->where('BIT_AND(u.type, :type) > 0')
            ->setParameter('type', User::TYPE_ARTIST)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
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
