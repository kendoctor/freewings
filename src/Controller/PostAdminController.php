<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostMedia;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="en|zh_CN"})
 */
class PostAdminController extends Controller
{
    /**
     * @Route("/{page}", name="post_index", methods="GET", requirements={"page"="\d"})
     * @param PostRepository $postRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(PostRepository $postRepository, Request $request, PaginatorInterface $paginator, $page=1): Response
    {
        $pagination = $paginator->paginate(
            $postRepository->findIt(),
            $page,
            10
        );

        return $this->render('post/admin/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="post_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(PostRepository $postRepository, Request $request): Response
    {
        $post = $postRepository->create();

        $form = $this->createForm(PostType::class, $post);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        //    $post->setCreatedBy($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('post/admin/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    private function generateUniqueFilename()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}/show", name="post_show", methods="GET")
     */
    public function show(Post $post): Response
    {
        return $this->render('post/admin/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods="GET|POST")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function edit(PostRepository $repository, Request $request, $id): Response
    {
        /** @var Post $post */
        $post = $repository->findOneWithTagsById($id);

        $originalImages = new ArrayCollection();

        foreach ($post->getImages() as $image) {
            $originalImages->add($image);
        }


        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            /** @var PostMedia $image */
            foreach($originalImages as $image)
            {
                if(false === $post->getImages()->contains($image))
                {
                    $post->getImages()->removeElement($image);
                    $image->setPost(null);
                    $em->persist($image);

                }
            }

            $em->flush();

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/admin/edit.html.twig', [
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


            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('admin_post_index');
    }



}
