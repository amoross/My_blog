<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_get",methods={"GET"})
     */
    public function index(ArticleRepository $postrepo): Response
    {
        $posts= $postrepo->findAll();
        return $this->json($posts,200,[],['groups'=> 'post:read']);
    }
}
