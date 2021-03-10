<?php

namespace App\Form;

use App\Entity\Ebook;
use App\Entity\Expertise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EbookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Titre du ebook *'
                    ]
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description du ebook *'
                    ]
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'choice',
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de publication *'
                    ]
            ])
            ->add('editorName', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de l\'editeur *'
                    ]
            ])
            ->add('author', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom de l\'auteur'
                    ]
            ])
            ->add('expertise', EntityType::class, [
                'class' => Expertise::class,
                'choice_label' => 'name',
                'expanded' => false,
                'by_reference' => true,
                'required' => true,
                'label' => "Expertise principale du ebook *",
            ])
            ->add('illustrationFile', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => false,
                'download_uri' => false,
                'required' => true,
                'label' => 'Ajouter l\'illustration du ebook * (format conseillé : 236 × 366)',
            ])
            ->add('documentEbookFile', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => false,
                'download_uri' => false,
                'required' => true,
                'label' => 'Ajouter le pdf *',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ebook::class,
        ]);
    }
}
