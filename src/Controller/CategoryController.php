<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/category/{id}', name:'single_category', requirements:['id' => '[0-9]+'])]
    public function single ($id, ManagerRegistry $manager): Response
    {
        // Charge une catégorie en fonction de l'id reçu
        $category = $manager->getRepository(Category::class)->find($id);
        // Si on trouve la catégorie, on affiche la page
        if ($category) {
            return $this->render('category/single.html.twig', [
                'category' => $category
            ]);
        } else {
            // Sinon on redirige l'utilisateur vers la page contenant toutes les catégories
            return $this->redirectToRoute('app_category');
        }
    }
}
