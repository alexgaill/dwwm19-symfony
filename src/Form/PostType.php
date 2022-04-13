<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre"
            ])
            ->add('content', TextareaType::class, [
                'label' => "Contenu de l'article"
            ])
            // Pour faire référence à une relation, on ajoute un champs EntityType
            ->add('category', EntityType::class, [
                // On précise à quelle entité on fait référence
                'class' => Category::class,
                // On indique l'information qui va s'afficher pour chaque option
                // ici le name des catégories
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
