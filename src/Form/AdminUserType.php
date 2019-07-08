<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',
                TextType::class,
                $this->getConfiguration("Prénon", "Le prénon de l'utilisateur ...")
            )
            ->add('lastName',
                TextType::class,
                $this->getConfiguration("Nom", "Le nom de l'utilisateur ...")
            )
            ->add('email',
                EmailType::class,
                $this->getConfiguration("Email", "L'email de l'utilisateur ...")
            )
            ->add('picture',
                UrlType::class,
                $this->getConfiguration("Photo de profil", "URL de l'avatar de l'utilisateur ...")
            )
            ->add('introduction',
                TextType::class,
                $this->getConfiguration("Introduction", "Présentation de l'utilisateur en quelques mots ...")
            )
            ->add('description',
                TextareaType::class,
                $this->getConfiguration("Description détaillée", "Présentation détaillée de l'utilisateur ...", [
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
