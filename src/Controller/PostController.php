<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
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
    #[IsGranted(data:'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour modifier un article");
            return $this->redirectToRoute('accueil');
        }
        $post = $manager->getRepository(Post::class)->find($id);
        
        if ($post){
            /**
             * Symfony s'attend à recevoir un fichier et non une chaine de caractère pour l'input file.
             * Pour corriger cette erreur, on doit se servir du nom du fichier qui est en BDD pour charger l'image qui est stockée
             * Pour ce faire, on utilise le composant File de HttpFoundation
             */
            $oldPictureExist = false;
            if (file_exists($this->getParameter('upload_dir').'/'.$post->getPicture()) && !is_dir($this->getParameter('upload_dir').'/'.$post->getPicture())) {
                $picture = new File($this->getParameter('upload_dir').'/'.$post->getPicture());
                $post->setPicture($picture);
                $oldPictureExist = true;
            }
            
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                // On récupère le nouveau fichier du formulaire
                $uploadedPicture = $form->get('picture')->getData();
                // S'il y a un novueau fichier
                if ($uploadedPicture) {
                    // On génère un novueau nom
                   $imageName = md5(uniqid()).'.'.$uploadedPicture->guessExtension();
                   // On déplace le nouveau fichier
                   $uploadedPicture->move($this->getParameter('upload_dir'), $imageName);

                   if ($oldPictureExist) {
                       // On supprime l'ancien fichier
                       unlink($this->getParameter('upload_dir').'/'.$post->getPicture()->getBasename());
                    }
                   // (string) permet de préciser que l'on veut utiliser la valeur de la variable jsute après comme une chaine de caractère
                   $post->setPicture((string) $imageName);
                } else {
                    // S'il n'y a pas de nouveau fichier, on récupère le nom du fichier déjà existant pour le restocker en BDD
                    $post->setPicture($picture->getBasename());
                }
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
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
