<?php

namespace Bkstg\FOSUserBundle\Form\Type;

use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\Type\ProductionRoleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionMembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('member', EntityType::class, [
                'class' => User::class,
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'required' => false,
                'choices' => [
                    'Editor' => 'GROUP_ROLE_EDITOR',
                    'Admin' => 'GROUP_ROLE_ADMIN',
                ],
            ])
            ->add('production_roles', CollectionType::class, [
                'entry_type' => ProductionRoleType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => ProductionMembership::STATUS_ACTIVE,
                    'Inactive' => ProductionMembership::STATUS_BLOCKED,
                ],
            ])
            ->add('expiry', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'BkstgFOSUserBundle',
            'data_class' => ProductionMembership::class,
        ));
    }
}
