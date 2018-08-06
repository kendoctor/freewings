<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AdvertisementRepository;
use App\Repository\BranchRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\ResumeRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingArtistRepository;
use App\Repository\WallPaintingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/{_locale}", defaults={"_locale"="zh_CN"}, name="home")
     * @param WallPaintingRepository $wallPaintingRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(WallPaintingRepository $wallPaintingRepository,
                          CategoryRepository $categoryRepository,
                          CustomerRepository $customerRepository,
                          UserRepository $userRepository,
                          AdvertisementRepository $advertisementRepository,
                          BranchRepository $branchRepository
    )
    {
        $em = $this->getDoctrine()->getManager();
        $postsRecommended = $em->getRepository(Post::class)->findRecommended();
        $wallPaintingCategoryRoot = $categoryRepository->getRoot('wall_painting');
        return $this->render('default/index.html.twig', [
            'advertisementsPublished' => $advertisementRepository->getPublished(),
            'artistsRecommended' => $userRepository->getArtistsRecommended(),
            'messagesRecommended' => [],
            'customersRecommended' => $customerRepository->getRecommended(),
            'wallPaintingCategoriesRecommended' => $categoryRepository->getRecommendedByRootAndOrderByWeight($wallPaintingCategoryRoot->getId()),
            'wallPaintingsRecommended' => $wallPaintingRepository->getRecommended(),
            'branches' => $branchRepository->findAll(),

        ]);
    }

    /**
     * @Route("/{_locale}/employment", defaults={"_locale"="zh_CN"}, name="employment")
     */
    public function employment(ResumeRepository $resumeRepository )
    {
        return $this->render('default/employment.html.twig', [
            'resumes' => $resumeRepository->getPublished()
        ]);
    }

    /**
     * @Route("/{_locale}/contact", defaults={"_locale"="zh_CN"}, name="contact")
     */
    public function contact(BranchRepository $branchRepository)
    {
        return $this->render('default/contact.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

    /**
     * @Route("/{_locale}/customer/{id}/show", defaults={"_locale"="zh_CN"}, name="customer_show")
     */
    public function customerShow(Customer $customer)
    {
        return $this->render('default/customer_show.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/{_locale}/customer", defaults={"_locale"="zh_CN"}, name="customer_index")
     */
    public function customerIndex(CustomerRepository $customerRepository)
    {

        return $this->render('default/customer_index.html.twig', [
            'customers' => $customerRepository->getRecommended(0)
        ]);
    }

    /**
     * @Route("/{_locale}/artist/{id}/page-{page}/show", defaults={"_locale"="zh_CN"}, name="artist_show")
     */
    public function artistShow(User $artist, WallPaintingArtistRepository $wallPaintingArtistRepository, PaginatorInterface $paginator, $page = 1)
    {
        $pagination = $paginator->paginate(
            $wallPaintingArtistRepository->getQueryByArtist($artist->getId()),
            $page
        );
        return $this->render('default/artist_show.html.twig', [
            'artist' => $artist,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{_locale}/team", defaults={"_locale"="zh_CN"}, name="artist_index")
     */
    public function artistIndex(UserRepository $userRepository)
    {
        return $this->render('default/artist_index.html.twig', [
            'artists' => $userRepository->getArtists()
        ]);
    }


    /**
     * @Route("/{_locale}/news/{id}/show", defaults={"_locale"="zh_CN"}, name="message_show")
     */
    public function messageShow()
    {

    }

    public function componentBranches(BranchRepository $branchRepository)
    {
        return $this->render('components/branches.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

}
