<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DemoController extends AbstractController
{
    /**
     * @Route("/demo", name="demo")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('demo/index.html.twig', [
            'controller_name' => 'DemoController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function Home(){
        return $this->render('demo/home.html.twig', []);
    }

     /**
     * @Route("/demo/new", name="demo_create")
     * @Route("/demo/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null ,Request $request, EntityManagerInterface $entityManager){
        if(!$article){
            $article = new Article();
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
           
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
        }

        return $this->render('demo/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/demo/{id}", name="show_article")
     */
    public function Show($id){
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        return $this->render('demo/show.html.twig', [
            'article' => $article
        ]);
    }

   
}
