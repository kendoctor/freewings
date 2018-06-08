<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Manager\CategoryManager;
use App\Repository\CategoryRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/admin/category/{_locale}", name="admin_", defaults={"_locale"="zh_CN"}, requirements={"_locale"="zh_CN|en"})
 */
class CategoryAdminController extends Controller
{


    /**
     * 分类列表
     *
     * 列出分类根节点，分类根节点为某种帖子的分类根节点
     * 譬如： 分类根节点名称为post，那么此节点下的分类用于Post的归类
     *
     * @Route("/{postType}", name="category_index", methods="GET")
     * @param CategoryManager $categoryManager
     * @param string $postType
     * @return Response
     */
    public function index(CategoryManager $categoryManager, $postType = 'post'): Response
    {
        //获取所有分类根节点并列出
        //默认一个分类根节点进行其分类编辑

        $root = $categoryManager->getRoot($postType);

        if(null === $root) {
            //throw exception postType is not valid
        }

        $categories = $categoryManager->getCategoryRepository()->getCategoryTree($root);

        return $this->render('category/admin/index.html.twig', [
            'root' => $root,
            'postType' => $postType,
            'categories' => $categories,
            'roots' => $categoryManager->getCategoryRepository()->getRoots(),
        ]);
    }

    public function categoryTypes(CategoryRepository $categoryRepository)
    {
        return $this->render('category/component/category_types.html.twig', [
            'roots' => $categoryRepository->getRoots()
        ]);
    }

    /**
     * @Route("/{postType}/new/{parentId}", name="category_new", methods="GET|POST")
     * @param CategoryManager $categoryManager
     * @param Request $request
     * @param string $postType
     * @param null $parentId
     * @return Response
     */
    public function new(CategoryManager $categoryManager, Request $request, $postType = 'post', $parentId = null): Response
    {

        /** @var Category $category */
        $category = $categoryManager->getCategoryRepository()->create();

        $root = $categoryManager->getRoot($postType);

        if (null === $root) {
            throw $this->createNotFoundException(sprintf('Root not found for the type: %s.', $postType));
        }

        if($parentId !== null)
        {
            $parent = $categoryManager->getCategoryRepository()->find($parentId);
            if(null === $parent)
            {
                throw $this->createNotFoundException(sprintf('Parent category not found for %s', $parentId));
            }
            $category->setParent($parent);
        }
        else
        {
            $category->setParent($root);
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryManager->getCategoryRepository()->persist($category);
            $categoryManager->getCategoryRepository()->flush();

            return $this->redirectToRoute('admin_category_index', ['postType' => $postType]);
        }

        return $this->render('category/admin/new.html.twig', [
            'category' => $category,
            'postType' => $postType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods="GET")
     */
    public function show(Category $category): Response
    {
        return $this->render('category/admin/show.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{postType}/{id}/edit", name="category_edit", methods="GET|POST")
     * @param CategoryManager $categoryManager
     * @param Request $request
     * @param Category $category
     * @param string $postType
     * @return Response
     */
    public function edit(CategoryManager $categoryManager, Request $request, Category $category, $postType='post'): Response
    {

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryManager->getCategoryRepository()->flush();

            return $this->redirectToRoute('admin_category_index', ['postType' => $postType]);
        }

        return $this->render('category/admin/edit.html.twig', [
            'category' => $category,
            'postType' => $postType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods="DELETE")
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
