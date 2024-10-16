<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use app\Form\CategoryType;
use App\Entity\Category;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;


class IndexController extends AbstractController
{
    private $entityManager;



    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;

    }



    #[Route('/', name: 'home')]
    public function home()
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }



    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();

 
        $form = $this->createForm(ArticleType::class, $article);


        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {

           
            $this->entityManager->persist($article);
            $this->entityManager->flush();

         
            return $this->redirectToRoute('home');
        }


        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/article/{id}', name: 'article_show')]

    public function show($id)
    {
        $article = $this->entityManager->getRepository(Article::class)

            ->find($id);

        return $this->render(
            'articles/show.html.twig',
            array('article' => $article)
        );
    }


    #[Route("/article/edit/{id}", name: "edit_article", methods: ['GET', 'POST'])]

    public function edit(Request $request, $id)
    {

        $article = $this->entityManager->getRepository(Article::class)->find($id);
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/article/delete/{id}", name: "delete_article", methods: ['POST'])]

    public function delete(Request $request, $id)
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
        $this->entityManager->remove($article);
        $this->entityManager->flush();
        return $this->redirectToRoute('home');
    }







    #[Route('/category', name: 'app_category_index', methods: ['GET'])]
    public function categoryIndex(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function newCategory(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show', methods: ['GET'])]
    public function showCategory(Category $category): Response
    {
        return $this->render('category/show.html.twig', ['category' => $category]);
    }

    #[Route('/category/edit/{id}', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', ['category' => $category, 'form' => $form]);
    }

    

    #[Route('/category/delete/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}

    
    

