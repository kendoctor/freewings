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
use App\Repository\TagRepository;
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
     * @Route("/{category}_{tag}/{page}/index", name="wall_painting_index", requirements={"page"="\d+"}, methods="GET")
     * @param CategoryRepository $categoryRepository
     * @param WallPaintingRepository $wallPaintingRepository
     * @param TagRepository $tagRepository
     * @param PaginatorInterface $paginator
     * @param string $category
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(CategoryRepository $categoryRepository,
                          WallPaintingRepository $wallPaintingRepository,
                          TagRepository $tagRepository,
                          PaginatorInterface $paginator,
                          $category = 'all',
                          $tag = 'none',
                          $page = 1)
    {
        $c = $category;

        if($category !== 'all')
        {
            $c = $categoryRepository->find($category);
            $category = $c->getId();
        }

        $pagination = $paginator->paginate(
            $wallPaintingRepository->getList($c, $tag),
            $page,
            12
        );

        return $this->render('wall_painting/index.html.twig', [
            'pagination' => $pagination,
            'tagsRecommended' => $tagRepository->getRecommend(),
            'category' => $category,
            'tag' => $tag,
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