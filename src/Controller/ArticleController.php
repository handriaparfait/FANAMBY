<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/', name: 'app_article')]
    public function index(ArticleRepository $articlerepo): Response
    {

        $article = $articlerepo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/index.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/article/{id<[0-9]+>}', name: 'app_show_article'/*, methods: 'GET|POST'*/)]
    public function detailArticle(Article $article)
    {
        return $this->render('article/detail.html.twig', ['article' => $article]);
    }

    #[Route('/create', name: 'app_create', methods: ["POST", "GET"])]
    public function create(Request $request): Response
    {
        

        $form = $this->createFormBuilder(new Article)
            ->add('Nom_Produit', TextType::class)
            ->add('Description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($form->getData());
            $this->em->flush();

            return $this->redirectToRoute('app_article');
            
        }

        return $this->render('article/create.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/article/{id<[0-9]+>}/edit', name: 'app_article_edit', methods: 'GET|POST')]
    public function editer(Article $article, Request $request): Response
    {

        $form = $this->createFormBuilder($article)
            ->add('Nom_Produit', TextType::class)
            ->add('Description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();

            return $this->redirectToRoute('app_article');
            
        }


        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'formulaire' => $form->createView(),
        ]);

    }

    #[Route('/article/{id<[0-9]+>}/delete', name: 'app_article_delete', methods: ['GET'])]
    public function delete(Request $request, Article $article)
    {     

        $this->em->remove($article);
        $this->em->flush();

             
        return $this->redirectToRoute('app_article');

    }




}
