<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Data\SearchExpertsData;
use App\Entity\Expertise;
use App\Entity\Provider;
use App\Entity\TypeService;
use App\Entity\User;

class SearchExpertsType extends AbstractType
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

            ->add('provider', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Provider::class,
                'expanded' => true,
                'multiple' => true
            ])

            ->add('expertise', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Expertise::class,
                'expanded' => true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchExpertsData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
