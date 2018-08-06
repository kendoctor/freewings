<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/message/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class MessageAdminController extends Controller
{
    /**
     * @Route("/{page}", requirements={"page"="\d+"}, name="message_index", methods="GET")
     */
    public function index(MessageRepository $messageRepository, PaginatorInterface $paginator, $page = 1): Response
    {
        return $this->render('message/admin/index.html.twig', [
            'pagination' => $paginator->paginate($messageRepository->getListQuery(), $page)
        ]);
    }

    /**
     * @Route("/new", name="message_new", methods="GET|POST")
     */
    public function new(MessageRepository $messageRepository, Request $request): Response
    {
        $message = $messageRepository->create();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('admin_message_index');
        }

        return $this->render('message/admin/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="message_show", methods="GET")
     */
    public function show(Message $message): Response
    {
        return $this->render('message/admin/show.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods="GET|POST")
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_message_edit', ['id' => $message->getId()]);
        }

        return $this->render('message/admin/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_delete", methods="DELETE")
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('admin_message_index');
    }
}
