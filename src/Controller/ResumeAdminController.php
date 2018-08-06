<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Repository\ResumeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/resume/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class ResumeAdminController extends Controller
{
    /**
     * @Route("/{page}", requirements={"page"="\d+"}, name="resume_index", methods="GET")
     */
    public function index(ResumeRepository $resumeRepository, PaginatorInterface $paginator, $page = 1): Response
    {
        return $this->render('resume/admin/index.html.twig', [
            'pagination' => $paginator->paginate($resumeRepository->getListQuery(), $page),
        ]);
    }

    /**
     * @Route("/new", name="resume_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resume);
            $em->flush();

            return $this->redirectToRoute('admin_resume_index');
        }

        return $this->render('resume/admin/new.html.twig', [
            'resume' => $resume,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resume_show", methods="GET")
     */
    public function show(Resume $resume): Response
    {
        return $this->render('resume/show.html.twig', ['resume' => $resume]);
    }

    /**
     * @Route("/{id}/edit", name="resume_edit", methods="GET|POST")
     */
    public function edit(Request $request, Resume $resume): Response
    {
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_resume_edit', ['id' => $resume->getId()]);
        }

        return $this->render('resume/admin/edit.html.twig', [
            'resume' => $resume,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resume_delete", methods="DELETE")
     */
    public function delete(Request $request, Resume $resume): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resume->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resume);
            $em->flush();
        }

        return $this->redirectToRoute('resume_index');
    }
}
