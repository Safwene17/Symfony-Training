<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    private $entityManager;
    private $formFactory;


    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory; 
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


        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
   
            $articleData = $form->getData(); 

            $this->entityManager->persist($articleData);
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


 
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Modifier'
            ])
            ->getForm();

  
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
    
}
