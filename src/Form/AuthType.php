<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AuthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajoute les champs communs à l'inscription et au login
      
            
        // Si l'option "is_registration" est vraie, ajouter les champs pour l'inscription
        if ($options['is_registration']) {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom',
                ])
                ->add('surname', TextType::class, [
                    'label' => 'Prénom',
                ]);
        }
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Email',
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
        ]);
        if ($options['is_registration']) {
            $builder
        ->add('vPassword', PasswordType::class, ['label' => 'Confirmation mot de passe']);
        }

        // Ajouter le bouton de soumission
        $builder->add('submit', SubmitType::class, [
            'label' => 'Continuer',
            'attr' => ['class' => 'button button-submit'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_registration' => false, // Par défaut, c'est pour le login
        ]);
    }
}
