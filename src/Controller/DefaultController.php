<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AdvertisementRepository;
use App\Repository\BranchRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\MessageRepository;
use App\Repository\ResumeRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingArtistRepository;
use App\Repository\WallPaintingRepository;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    private function getSettingManager()
    {
        return $this->get('phpmob.settings.manager');
    }


    /**
     * @Route("/{_locale}", defaults={"_locale"="zh_CN"}, name="home")
     * @param WallPaintingRepository $wallPaintingRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @param AdvertisementRepository $advertisementRepository
     * @param BranchRepository $branchRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function index(WallPaintingRepository $wallPaintingRepository,
                          MessageRepository $messageRepository,
                          CategoryRepository $categoryRepository,
                          CustomerRepository $customerRepository,
                          UserRepository $userRepository,
                          AdvertisementRepository $advertisementRepository,
                          BranchRepository $branchRepository
    )
    {
        $em = $this->getDoctrine()->getManager();
        $settingManager = $this->getSettingManager();

        $postsRecommended = $em->getRepository(Post::class)->findRecommended();
        $wallPaintingCategoryRoot = $categoryRepository->getRoot('wall_painting');
        return $this->render('default/index.html.twig', [
            'advertisementsPublished' => $advertisementRepository->getPublished($settingManager->get('homepage.advertisement_limit')),
            'artistsRecommended' => $userRepository->getArtistsRecommended($settingManager->get('homepage.artist_row_limit') * 12),
            'messagesRecommended' => $messageRepository->getRecommend($settingManager->get('homepage.message_row_limit') * 4),
            'customersRecommended' => $customerRepository->getRecommended($settingManager->get('homepage.customer_row_limit') * 6),
            'wallPaintingCategoriesRecommended' => $categoryRepository->getRecommendedByRootAndOrderByWeight($wallPaintingCategoryRoot->getId(), $settingManager->get('homepage.wall_painting_category_row_limit') * 4),
            'wallPaintingsRecommended' => $wallPaintingRepository->getRecommended($settingManager->get('homepage.wall_painting_row_limit') * 4),
            'branches' => $branchRepository->findAll(),

        ]);
    }

    /**
     * @Route("/{_locale}/static-message-category/{token}", defaults={"_locale"="zh_CN"}, name="static_message_category")
     */
    public function staticMessageCategory(CategoryRepository $categoryRepository, MessageRepository $messageRepository, $token)
    {
        $category = $categoryRepository->getByToken($token);

        if(!$category)
            throw  $this->createNotFoundException(sprintf('Category with token %s not found', $token));

        /** @var Collection $messages */
        $messages = $messageRepository->getByCategory($category->getId());

        if(empty($messages))
        {
            throw  $this->createNotFoundException(sprintf('No messages in category %s', $category->getName()));
        }

        return $this->redirectToRoute('static_page_show', ['id' => $messages[0]->getId()]);
    }

    /**
     * @Route("/{_locale}/static/{id}", defaults={"_locale"="zh_CN"}, name="static_page_show")
     */
    public function staticPageShow(MessageRepository $messageRepository, Message $message)
    {
        return $this->render('default/static_page_show.html.twig', [
            'messages' => $messageRepository->getByCategory($message->getCategory()->getId(), false),
            'message' => $message
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
    public function messageShow(Message $message)
    {
        return $this->render('default/message_show.html.twig', [
            'message' => $message
        ]);
    }


    /**
     * @Route("/{_locale}/news/{category}_{page}", defaults={"_locale"="zh_CN"}, name="message_index")
     */
    public function messageIndex(CategoryRepository $categoryRepository, MessageRepository $messageRepository, PaginatorInterface $paginator, $page = 1, $category = 'all')
    {
        return $this->render('default/message_index.html.twig', [
            'categories' => $categoryRepository->getMessageCategoryTree(),
            'pagination' => $paginator->paginate($messageRepository->getIndexQuery($category), $page)
        ]);
    }


    public function componentBranches(BranchRepository $branchRepository)
    {
        return $this->render('components/branches.html.twig', [
            'branches' => $branchRepository->findAll()
        ]);
    }

}
