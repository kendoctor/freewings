<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/media/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="zh_CN|en"})
 */
class MediaAdminController extends Controller
{
    /**
     * @Route("/{page}", requirements={"page"="\d+"},  name="media_index", methods="GET")
     */
    public function index(MediaRepository $mediaRepository, PaginatorInterface $paginator, $page = 1): Response
    {
        return $this->render('media/admin/index.html.twig', [
            'pagination' => $paginator->paginate($mediaRepository->getListQuery(), $page)
        ]);
    }

    /**
     * @Route("/new", name="media_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $medium = new Media();
        $form = $this->createForm(MediaType::class, $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($medium);
            $em->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('media/admin/new.html.twig', [
            'medium' => $medium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="media_show", methods="GET")
     */
    public function show(Media $medium): Response
    {
        return $this->render('media/show.html.twig', ['medium' => $medium]);
    }

    /**
     * @Route("/{id}/edit", name="media_edit", methods="GET|POST")
     */
    public function edit(Request $request, Media $medium): Response
    {
        $form = $this->createForm(MediaType::class, $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_media_edit', ['id' => $medium->getId()]);
        }

        return $this->render('media/admin/edit.html.twig', [
            'medium' => $medium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="media_delete", methods="DELETE")
     */
    public function delete(Request $request, Media $medium): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medium->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($medium);
            $em->flush();
        }

        return $this->redirectToRoute('media_index');
    }
}
