<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }


    public function create()
    {
        $post = new Post();

        $tokens = ['cover', 'thumb', 'list'];

        foreach ($tokens as $token) {
            $image = new PostMedia();
            $image->setToken($token);
            $post->addImage($image);
        }

        return $post;
    }

    public function findIt()
    {
        return $this->createQueryBuilder('p')
            ->where('p INSTANCE OF App\Entity\Post')
            ->getQuery()
            ;
    }

    public function findOneWithTagsById($id)
    {
        return $this->createQueryBuilder('p')
         //   ->addSelect('pt')
           //    ->leftJoin('p.postTags', 'pt')
            ->where("p.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;

    }

    public function findRecommended($limit = 10)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * 获取唯一内容,如内容头条，分类下内容头条，或者通过token指定内容
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImportantOne($contentType=null, $token=null)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }




//    /**
//     * @return Post[] Returns an array of Post objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
