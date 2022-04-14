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
        } else {   
            $this->addFlash("danger", "L'article demandé n'existe pas");
            return $this->redirectToRoute("app_post");
        }
    }

    #[Route('/post/save', name:'save_post', methods: ['GET', 'POST'])]
    public function save(Request $request, ManagerRegistry $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            // On récupère les informations de l'image reçue à travers le form
            $picture = $form->get('picture')->getData();
            if ($picture) {
                // On génère un nouveau nom de fichier pour éviter les conflits entre les fichiers existants
                $imageName = md5(uniqid()). "." .$picture->guessExtension();
                // On déplace le fichier dans le dossier définit par le paramètre upload_dir
                // On copie ce fichier avec le nom qui vient d'être généré
                $picture->move($this->getParameter('upload_dir'), $imageName);
                // On enregistre en BDD le nouveau nom de fichier
                $post->setPicture($imageName);
            }

            $post->setCreatedAt(new \DateTime());
            $em = $manager->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash("success", "L'article a été ajouté avec succés");
            return $this->redirectToRoute('single_post', ['id' => $post->getId()], 201);
        }
        return $this->renderForm('post/save.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/post/{id}/update', name:'update_post', requirements:['id' => "[0-9]+"], methods:["GET", "POST"])]
    public function update ($id, ManagerRegistry $manager, Request $request):Response
    {
        $post = $manager->getRepository(Post::class)->find($id);
        if ($post){

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $manager->getManager();
                $em->persist($post);
                $em->flush();
                $this->addFlash("success", "L'article a été modifié");
                return $this->redirectToRoute("single_post", ['id' => $post->getId()]);
            }
            
            return $this->renderForm('post/update.html.twig', [
                'form' => $form,
                'post' => $post
            ]);
        } else {
            $this->addFlash("danger", "L'article demandé n'existe pas");
            return $this->redirectToRoute('app_post');
        }
    }

    #[Route("/post/{id}/delete", name:'delete_post', requirements:['id' => "[0-9]+"], methods:["GET"])]
    public function delete($id, ManagerRegistry $manager): Response
    {
        $post = $manager->getRepository(Post::class)->find($id);
        if ($post) {

            $em = $manager->getManager();
            $em->remove($post);
            $em->flush();
            $this->addFlash("success", "L'article a été supprimé");
        } else {
            $this->addFlash("danger", "L'article demandé n'existe pas");
        }
            return $this->redirectToRoute('app_post');
    }
}
