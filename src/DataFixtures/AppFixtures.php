<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On charge la librairie faker qui permet de générer de fausses données
        $faker = Factory::create();
        for ($i=0; $i < 10; $i++) { 
            // On crée une nouvelle catégorie
            $category = new Category;
            // On ajoute un nom à la catégorie grâce à faker
            $category->setName($faker->words(2, true));
            // On ajoute la catégorie à la file d'attente de l'enregistrement
            $manager->persist($category);
        }

        for ($j=0; $j < 10; $j++) { 
            $post = new Post;
            $post->setTitle($faker->words(5, true))
                ->setContent($faker->paragraphs(3, true))
                ->setCreatedAt($faker->dateTime());

            $manager->persist($post);
        }
        // On exécute tous les enregistrements
        $manager->flush();

        // On utilise la commande php bin/console doctrine:fixtures:load pour exécuter ce script
    }
}
