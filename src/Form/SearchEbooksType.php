<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Data\SearchEbooksData;
use App\Entity\Ebook;
use App\Entity\Expertise;

class SearchEbooksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Recherche rapide'
                    ]
            ])

            ->add('expertise', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Expertise::class,
                'expanded' => true,
                'multiple' => true
            ])

            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchEbooksData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
