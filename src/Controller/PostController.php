<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/post/{id}', name:"single_post", requirements:['id'=> "[0-9]+"])]
    public function single($id, ManagerRegistry $manager): Response
    {
        $post = $manager->getRepository(Post::class)->find($id);
        if ($post) {
            return $this->render('post/single.html.twig', [
                'post' => $post
            ]);
        }

        return $this->redirectToRoute("app_post");
    }

    #[Route('/post/save', name:'save_post', methods: ['GET', 'POST'])]
    public function save(Request $request, ManagerRegistry $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $em = $manager->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('single_post', ['id' => $post->getId()], 201);
        }
        return $this->renderForm('post/save.html.twig', [
            'form' => $form
        ]);
    }
}
