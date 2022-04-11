<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(ManagerRegistry $manager): Response
    {
        $posts = $manager->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', [
            'postList' => $posts
        ]);
    }
}
