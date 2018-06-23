<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\BranchRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(BranchRepository $branchRepository)
    {
        return $this->render('default/contact.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

    /**
     * @Route("/customer/{id}/show", name="customer_show")
     */
    public function customerShow(Customer $customer)
    {
        return $this->render('default/customer_show.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/artist/{id}/show", name="artist_show")
     */
    public function artistShow(User $artist)
    {
        return $this->render('default/artist_show.html.twig', [
            'artist' => $artist
        ]);
    }

    public function artistIndex()
    {

    }

    public function componentBranches(BranchRepository $branchRepository)
    {
        return $this->render('components/branches.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

}
