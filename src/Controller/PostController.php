<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="post_index", methods="GET")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', ['posts' => $postRepository->findAll()]);
    }

    /**
     * @Route("/new", name="post_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $titlePictureFile */
//            $titlePictureFile = $post->getTitlePictureFile();
//
//            if($titlePictureFile) {
//                $titlePictureFilename = $this->generateUniqueFilename().'.'.$titlePictureFile->guessExtension();
//
//                $titlePictureFile->move(
//                    $this->getParameter("post.title_picture"),
//                    $titlePictureFilename
//                );
//
//                $post->setTitlePicture($titlePictureFilename);
//            }


            $post->setCreatedBy($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    private function generateUniqueFilename()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="post_show", methods="GET")
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods="GET|POST")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            //delete previous uploaded file if exist
//            if($post->getTitlePicture())
//            {
//                //get absolute path of the image
//                $previousTitlePictureFile = $this->getParameter('post.title_picture') . '/' . $post->getTitlePicture();
//                if(file_exists($previousTitlePictureFile))
//                {
//                    unlink($previousTitlePictureFile);
//                }
//            }
//            $titlePictureFile = $post->getTitlePictureFile();
//            /** @var UploadedFile $titlePictureFile */
//            if($titlePictureFile) {
//                $titlePictureFilename = $this->generateUniqueFilename().'.'.$titlePictureFile->guessExtension();
//
//                $titlePictureFile->move(
//                    $this->getParameter("post.title_picture"),
//                    $titlePictureFilename
//                );
//
//                $post->setTitlePicture($titlePictureFilename);
//            }

            $post->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="post_delete", methods="DELETE")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
//            //delete previous uploaded file if exist
//            if($post->getTitlePicture())
//            {
//                //get absolute path of the image
//                $previousTitlePictureFile = $this->getParameter('post.title_picture') . '/' . $post->getTitlePicture();
//                if(file_exists($previousTitlePictureFile))
//                {
//                    unlink($previousTitlePictureFile);
//                }
//            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }


}
