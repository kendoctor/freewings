<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\CustomerMedia;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function create()
    {
        $customer = new Customer();

        $tokens = ['cover', 'thumb', 'list'];

        foreach ($tokens as $token) {
            $image = new CustomerMedia();
            $image->setMedia(new Media());
            $image->setToken($token);
            $customer->addImage($image);
        }

        return $customer;
    }

    public function persist($entity)
    {
        $this->_em->persist($entity);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getRecommended($limit = 12)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isRecommended = true');
        if($limit > 0)
        {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getByOldId($oldId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.oldId = :oldId')
            ->setParameter('oldId', $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getListQuery()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ;
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
