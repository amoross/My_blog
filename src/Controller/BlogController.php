<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'ControllerBlogController',
            'articles' => $articles
            ]
        );
    }

    /**
     * @Route("/",name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig',[
            'title' => 'Bienvenue sur le bloc'
        ]);

    }

    /**
     * @Route("/blog/new/a",name="blog_creat")
     */
    public function form(Request $request, EntityManagerInterface $manager)
    {
            $article = new Article();

        $form=$this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id'=> $article->getId()]);
        }
        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode'=> $article->getId()!== null
        ]);

    }
    /**
     * @Route("/blog/{id}",name="blog_show",requirements={"id":"\d+"})
     */
    public function show( Article $article,Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment );

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTime())
                ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return  $this->redirectToRoute('blog_show', ['id' => $article->getId() ]);
        }

        return $this->render('blog/show.html.twig',[
            'article'=>$article ,
            'commentForm'=> $form->createView()
        ]);

    }

}
