<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\Message;
use App\Entity\PostMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function create()
    {
        $message = new Message();

        return $message;
    }

    public function getByOldId($oldId)
    {
        return $this->createQueryBuilder('m')
            ->where('m.oldId = :oldId')
            ->setParameter('oldId', $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getByCategory($categoryId, $only=true)
    {
        $query = $this->createQueryBuilder('m')
            ->where('IDENTITY(m.category) = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
        ;

        if($only)
            $query->setMaxResults(1);

        return $query->getResult();

    }

    public function getListQuery()
    {
        return $this->createQueryBuilder('m')
            ->addOrderBy('m.updatedAt', 'DESC')
            ->getQuery();
    }

    public function persist($entity)
    {
        $this->_em->persist($entity);

    }

    public function flush()
    {
        $this->_em->flush();
    }


}
