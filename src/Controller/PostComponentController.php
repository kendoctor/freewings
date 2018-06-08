<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/28/18
 * Time: 10:46 PM
 */

namespace App\Controller;


use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PostComponentController extends Controller
{

    /**
     * 获取唯一的重要内容
     *
     * @param PostRepository $repository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function importantOne(PostRepository $repository)
    {
        $post = $repository->findImportantOne();
        return $this->render('post/component/important_one.html.twig', compact('post'));
    }

}