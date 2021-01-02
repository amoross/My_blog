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
     * @Route("/blog/{id}/edit" , name="blog_edit")
     */
    public function form(Article $article = null,Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (strcmp($request->get('_route'), 'blog_edit') == 0) {
            if (!$article) {
                throw new \Exception('Article innexistant !');
            }
            elseif ($article->getUsers()->getId() != $this->getUser()->getId())
            {
                throw new \Exception('Vous n\'êtes pas l\'auteur de cet article !');
            }
        }
        if (!$article) {
            $article = new Article();
        }

        $form=$this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $article->setUser($this->getUser());
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
//         $id=$article->getUsers()->getId();
        return $this->render('blog/show.html.twig',[
            'article'=>$article ,
//            'id' =>$id,
            'commentForm'=> $form->createView()
        ]);

    }

    /**
     * @Route("/blog/{id}/delete", name="blog_delete")
     */
    public function deleteArticle (Article $article,EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($article->getUsers()->getId() != $this->getUser()->getId()) {
            throw new \Exception('Vous n\'êtes pas l\'auteur de cet article !');
        }
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('blog');
    }

}
