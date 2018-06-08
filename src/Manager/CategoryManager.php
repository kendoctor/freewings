<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/8/18
 * Time: 5:28 AM
 */

namespace App\Manager;


use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;

class CategoryManager
{
    private $categoryRepository;
    private $tagRepository;


    public function __construct(CategoryRepository $categoryRepository, TagRepository $tagRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }

    /**
     * @return TagRepository
     */
    public function getTagRepository(): TagRepository
    {
        return $this->tagRepository;
    }


    public function getRoot($postType)
    {
        $root = $this->categoryRepository->getRoot($postType);

        if (null === $root) {
            $root = new Category();
            $root->setName($postType);

            $this->categoryRepository->persist($root);
            $this->categoryRepository->flush();
        }

        return $root;
    }
}