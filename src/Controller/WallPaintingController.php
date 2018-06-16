<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/12/18
 * Time: 12:21 AM
 */

namespace App\Controller;


use App\Entity\WallPainting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("wall-painting/{_locale}", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class WallPaintingController extends Controller
{
    /**
     * @Route("/", name="wall_painting_index", methods="GET")
     */
    public function index()
    {

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