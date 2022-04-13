<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(ManagerRegistry $manager): Response
    {
        // Récupère toutes les catégories présentes en BDD
        $categories = $manager->getRepository(Category::class)->findAll();
        // Génère le template de la page
        return $this->render('category/index.html.twig', [
            'categoriesList' => $categories,
        ]);
    }

    #[Route('/category/{id}', name:'single_category', requirements:['id' => '\d+'])]
    public function single (int $id, ManagerRegistry $manager): Response
    {
        // Charge une catégorie en fonction de l'id reçu
        $category = $manager->getRepository(Category::class)->find($id);
        // Si on trouve la catégorie, on affiche la page
        if ($category) {
            return $this->render('category/single.html.twig', [
                'category' => $category
            ]);
        } else {
            $this->addFlash("danger", "La catégorie demandée n'existe pas");
            // Sinon on redirige l'utilisateur vers la page contenant toutes les catégories
            return $this->redirectToRoute('app_category');
        }
    }
    
    #[Route('/category/save', name: 'save_category', methods:["GET", "POST"])]
    public function save (Request $request, ManagerRegistry $manager): Response
    {
        // On créé une nouvelle catégorie
        $category = new Category;
        // On créé le formulaire auquel on associe la catégorie qui récupèrera les informations
        $form = $this->createFormBuilder($category)
        // On ajoute l'input pour le name de la catégorie
        // Attention n'oubliez pas le: use Symfony\Component\Form\Extension\Core\Type\TextType;
                ->add('name', TextType::class, [
                    'label' => 'Nom de la catégorie',
                    'required'=> true
                ])
                ->add('submit', SubmitType::class, [
                    'label' => "Ajouter",
                    'attr' => [
                        'class' => "btn btn-primary"
                    ]
                ])
                // Génère l'objet formulaire
                ->getForm();
        // Associe les données provenant de $_POST à notre formulaire 
        // et plus particulièrement à la catégorie associée
        $form->handleRequest($request);
        // $form->isSubmitted() vérifie que le formulaire a bien été soumis par le button
        // $form->isValid() vérifie que les données reçues correspondent à toutes les contraintes indiquées 
        // pour chaque propriété
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em = $manager->getManager();
            $em->persist($category);
            $em->flush();
            // Ajout d'un message flash pour prévenir du résultat de l'opération
            $this->addFlash("success", "La catégorie a bien été ajoutée");
            return $this->redirectToRoute('app_category');
        }

        return $this->renderForm('category/save.html.twig',[
            'formCategory' => $form,
            'category' => $category
        ]);
    }

    #[Route('/category/{id}/update', name:'update_category', requirements:['id' => "[0-9]+"], methods:["GET", "POST"])]
    public function update ($id, ManagerRegistry $manager, Request $request): Response
    {
        $category = $manager->getRepository(Category::class)->find($id);

        if ($category) {

            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $manager->getManager();
                $em->persist($category);
                $em->flush();

                $this->addFlash("success", "La catégorie a bien été modifiée");
                return $this->redirectToRoute("single_category", ['id' => $category->getId()]);
            }

            return $this->renderForm('category/update.html.twig', [
                'formCategory' => $form,
                'category' => $category
            ]);
        } else {
            $this->addFlash("danger", "La catégorie demandée n'existe pas");
            return $this->redirectToRoute('app_category');
        }

    }

    #[Route("/category/{id}/delete", name:'delete_category', requirements:['id' => "[0-9]+"], methods:["GET"])]
    public function delete($id, ManagerRegistry $manager): Response
    {
        $category = $manager->getRepository(Category::class)->find($id);
        if ($category) {
            $em = $manager->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash("success", "La catégoriea été supprimée");
        } else {
            $this->addFlash("danger", "La catégorie demandée n'existe pas");
        }
        return $this->redirectToRoute('app_category');
    }
}
