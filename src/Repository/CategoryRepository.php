<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryMedia;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends NestedTreeRepository implements ServiceEntityRepositoryInterface
{

    public function __construct(RegistryInterface $registry)
    {
        $manager = $registry->getManagerForClass(Category::class);


        parent::__construct($manager, $manager->getClassMetadata(Category::class));

    }

    public function create()
    {
        $category = new Category();

        $tokens = ['icon', 'thumb', 'list'];

        foreach ($tokens as $token) {
            $image = new CategoryMedia();
            $image->setMedia(new Media());
            $image->setToken($token);
            $category->addImage($image);
        }

        return $category;
    }


    public function persist($entity)
    {
        $this->_em->persist($entity);
    }

    public function flush($entity = null)
    {
        $this->_em->flush($entity);
    }

    /**
     * 获取所有分类根节点
     */
    public function getRoots()
    {
        return $this->getRootNodesQueryBuilder()
            ->getQuery()
            ->getResult()
            ;
    }


    public function getByOldNewsCategoryId($oldId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.oldNewsCategoryId = :oldId')
            ->setParameter('oldId' , $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getByOldId($oldId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.oldId = :oldId')
            ->setParameter('oldId' , $oldId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getByToken($token)
    {
        return $this->createQueryBuilder('c')
            ->where('c.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 通过帖子类型获取其分类根节点
     * 如果根节点不存在，初始化节点
     *
     * @param string $postType
     * @param null $creator
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getRoot($postType = 'post', $creator = null)
    {

        $root = $this->getRootNodesQueryBuilder()
            ->where('node.name = :name')
            ->setParameter('name', $postType)
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if (null === $root) {
            $root = new Category();
            if($creator)
                $root->setCreatedBy($creator);
            $root->setName($postType);
            $this->_em->persist($root);
            $this->_em->flush();
        }

        return $root;

    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getRecommendedByRootAndOrderByWeight($rootId, $limit = 7)
    {
        return $this->createQueryBuilder('c')
            ->where('c.isRecommended = true')
            ->andWhere('IDENTITY(c.root) = :rootId')
            ->setParameter('rootId', $rootId)
            ->setMaxResults($limit)
            ->orderBy('c.weight', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findUniqueByIdWithTag($id)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('t')
            ->leftJoin('c.tag', 't')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getChoicesList($hasRoot = false, $postType='post')
    {
        return $this->getChildrenQueryBuilder($this->getRoot($postType), false, null, 'ASC', $hasRoot)
            ->getQuery()
            ->getResult()
        ;
        //return $this->getChildren($this->getRoot(), false, null, 'ASC', $hasRoot);
    }

    /**
     * 获取树形结构的分类
     *
     * @param $root
     * @return mixed
     */
    public function getCategoryTree($root)
    {
        $this->_em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');

        $tree = $this->getChildrenQueryBuilder($root)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree')
        ;

        return $tree;

    }

    public function getMessageCategoryTree()
    {
        $root = $this->getRoot('message');
        $this->_em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');

        $tree = $this->getChildrenQueryBuilder($root)
            ->andWhere('node.isStatic = false')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree')
        ;

        return $tree;
    }

    public function getCategoryTreeByType($type = 'wall_painting')
    {
        $root = $this->getRoot($type);
        return $this->getCategoryTree($root);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
