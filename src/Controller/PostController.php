<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/28/18
 * Time: 12:10 PM
 */

namespace App\Controller;


use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="frontend_")
 *
 * Class PostController
 * @package App\Controller\Frontend
 */
class PostController extends Controller
{
    /**
     * @Route("/{id}", name="post_show", methods="GET")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post)
    {
        return $this->render('post/show.html.twig', compact('post'));
    }

}