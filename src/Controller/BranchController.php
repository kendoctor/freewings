<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/20/18
 * Time: 9:30 AM
 */

namespace App\Controller;


use App\Entity\Branch;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}/branch", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class BranchController extends Controller
{
    /**
     * @Route("/", name="branch_index", methods="GET")
     */
    public function index()
    {
        return $this->render('branch/index.html.twig', [

        ]);
    }
}