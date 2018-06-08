<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $postsRecommended = $em->getRepository(Post::class)->findRecommended();

        return $this->render('default/index.html.twig', [
            'postsRecommended' => $postsRecommended,
            'controller_name' => 'DefaultController',
        ]);
    }

}
