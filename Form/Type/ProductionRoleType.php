<?php

namespace Bkstg\FOSUserBundle\Form\Type;

use Bkstg\FOSUserBundle\Entity\ProductionRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('designation', ChoiceType::class, [
                'choices' => [
                    'Crew' => 'Crew',
                    'Cast' => 'Cast'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductionRole::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'production_role';
    }
}
