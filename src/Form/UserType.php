<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('POST')
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom',
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'empty_data' => '',
            ])
            ->add('status', TextType::class, [
                'label' => 'Statut',
                'empty_data' => '',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => "date d'entrée",
                'empty_data' => null,
                'invalid_message' => 'Veuillez entrer une date valide.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'button button-submit'],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
