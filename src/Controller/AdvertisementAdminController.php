<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/advertisement/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class AdvertisementAdminController extends Controller
{
    /**
     * @Route("/{page}", requirements={"page"="\d+"}, name="advertisement_index", methods="GET")
     */
    public function index(AdvertisementRepository $advertisementRepository, PaginatorInterface $paginator, $page = 1): Response
    {
        return $this->render('advertisement/admin/index.html.twig',
            [
                'pagination' => $paginator->paginate($advertisementRepository->getListQuery(), $page)
            ]);
    }

    /**
     * @Route("/new", name="advertisement_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $advertisement = new Advertisement();
        $form = $this->createForm(AdvertisementType::class, $advertisement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advertisement);
            $em->flush();

            return $this->redirectToRoute('admin_advertisement_index');
        }

        return $this->render('advertisement/admin/new.html.twig', [
            'advertisement' => $advertisement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advertisement_show", methods="GET")
     */
    public function show(Advertisement $advertisement): Response
    {
        return $this->render('advertisement/show.html.twig', ['advertisement' => $advertisement]);
    }

    /**
     * @Route("/{id}/edit", name="advertisement_edit", methods="GET|POST")
     */
    public function edit(Request $request, Advertisement $advertisement): Response
    {
        $form = $this->createForm(AdvertisementType::class, $advertisement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_advertisement_edit', ['id' => $advertisement->getId()]);
        }

        return $this->render('advertisement/admin/edit.html.twig', [
            'advertisement' => $advertisement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advertisement_delete", methods="DELETE")
     */
    public function delete(Request $request, Advertisement $advertisement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advertisement->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($advertisement);
            $em->flush();
        }

        return $this->redirectToRoute('advertisement_index');
    }
}
