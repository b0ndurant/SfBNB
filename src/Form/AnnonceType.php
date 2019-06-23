<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    /**
     * configure field
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = [])
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ],
        ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration("Titre", "Votre titre")
            )
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration("Adresse Web", "Votre slug", [
                    "required" => false,
                ])
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration("URL de l'image principal", "Donnez l'adresse d'une image")
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration("Introduction", "Donnez une description globale de l'annonce")
            )
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration("Description", "Donnez une description detaillée")
            )
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfiguration("Nombre de chambres", "Le nombre de chambres disponnibles")
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration("Prix par nuit", "Indiquez votre prix")
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
