<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/12/18
 * Time: 12:21 AM
 */

namespace App\Controller;


use App\Entity\WallPainting;
use App\Repository\CategoryRepository;
use App\Repository\WallPaintingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}/wall-painting", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class WallPaintingController extends Controller
{
    /**
     * @Route("/{category}/{page}/index", name="wall_painting_index", requirements={"page"="\d+"}, methods="GET")
     */
    public function index(CategoryRepository $categoryRepository, WallPaintingRepository $wallPaintingRepository,
                          PaginatorInterface $paginator,
                          $category = 'all',
                          $page = 1)
    {
        if($category !== 'all')
        {
            $category = $categoryRepository->find($category);
        }

        $pagination = $paginator->paginate(
            $wallPaintingRepository->getList($category),
            $page,
            12
        );


        return $this->render('wall_painting/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categoryRepository->getCategoryTreeByType()
        ]);
    }

    /**
     * @Route("/{id}/show", name="wall_painting_show", methods="GET")
     */
    public function show(WallPainting $wallPainting)
    {
        return $this->render('wall_painting/show.html.twig', [
            'wallPainting' => $wallPainting
        ]);
    }

    
}