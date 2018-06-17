<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\BranchRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param WallPaintingRepository $wallPaintingRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(  WallPaintingRepository $wallPaintingRepository,
                            CategoryRepository $categoryRepository,
                            CustomerRepository $customerRepository,
                            UserRepository $userRepository,
                            BranchRepository $branchRepository
    )
    {
        $em = $this->getDoctrine()->getManager();
        $postsRecommended = $em->getRepository(Post::class)->findRecommended();
        $wallPaintingCategoryRoot = $categoryRepository->getRoot('wall_painting');
        return $this->render('default/index.html.twig', [
            'artistsRecommended' => $userRepository->getArtistsRecommended(),
            'messagesRecommended' => [],
            'customersRecommended' => $customerRepository->getRecommended(),
            'wallPaintingCategoriesRecommended' => $categoryRepository->getRecommendedByRootAndOrderByWeight($wallPaintingCategoryRoot->getId()),
            'wallPaintingsRecommended' => $wallPaintingRepository->getRecommended(),
            'branches' => $branchRepository->findAll(),
            'controller_name' => 'DefaultController',
        ]);
    }

    public function componentBranches(BranchRepository $branchRepository)
    {
        return $this->render('components/branches.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

}
