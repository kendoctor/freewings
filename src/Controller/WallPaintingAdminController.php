<?php

namespace App\Controller;

use App\Entity\WallPainting;
use App\Entity\WallPaintingArtist;
use App\Form\WallPaintingType;
use App\Repository\WallPaintingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/wall-painting/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class WallPaintingAdminController extends Controller
{
    /**
     * @Route("/", name="wall_painting_index", methods="GET")
     */
    public function index(WallPaintingRepository $wallPaintingRepository): Response
    {
        return $this->render('wall_painting/admin/index.html.twig', ['wall_paintings' => $wallPaintingRepository->findAll()]);
    }

    /**
     * @Route("/new", name="wall_painting_new", methods="GET|POST")
     */
    public function new(WallPaintingRepository $wallPaintingRepository, Request $request): Response
    {
        $wallPainting = $wallPaintingRepository->create();
        $form = $this->createForm(WallPaintingType::class, $wallPainting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wallPainting);
            $em->flush();

            return $this->redirectToRoute('admin_wall_painting_index');
        }

        return $this->render('wall_painting/admin/new.html.twig', [
            'wall_painting' => $wallPainting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wall_painting_show", methods="GET")
     */
    public function show(WallPainting $wallPainting): Response
    {
        return $this->render('wall_painting/show.html.twig', ['wall_painting' => $wallPainting]);
    }

    /**
     * @Route("/{id}/edit", name="wall_painting_edit", methods="GET|POST")
     * @param Request $request
     * @param WallPainting $wallPainting
     * @return Response
     */
    public function edit(Request $request, WallPainting $wallPainting): Response
    {

        $form = $this->createForm(WallPaintingType::class, $wallPainting);
        $originalWallPaintingArtits = new ArrayCollection();

        foreach ($wallPainting->getWallPaintingArtists() as $wallPaintingArtist) {
            $originalWallPaintingArtits->add($wallPaintingArtist);
        }


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var WallPaintingArtist $wallPaintingArtist */
            foreach ($originalWallPaintingArtits as $wallPaintingArtist) {

                if (false === $wallPainting->getWallPaintingArtists()->contains($wallPaintingArtist)) {
                    $wallPainting->getWallPaintingArtists()->removeElement($wallPaintingArtist);
                    $wallPaintingArtist->setWallPainting(null);
                    $em->remove($wallPaintingArtist);
                }
            }
            $em->flush();

            return $this->redirectToRoute('admin_wall_painting_edit', ['id' => $wallPainting->getId()]);
        }

        return $this->render('wall_painting/admin/edit.html.twig', [
            'wall_painting' => $wallPainting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wall_painting_delete", methods="DELETE")
     */
    public function delete(Request $request, WallPainting $wallPainting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wallPainting->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wallPainting);
            $em->flush();
        }

        return $this->redirectToRoute('wall_painting_index');
    }
}
