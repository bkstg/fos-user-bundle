<?php

namespace Bkstg\FOSUserBundle\Form;

use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\ProductionRoleType;
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
    /**
     * {@inheritdoc}
     *
     * @param  FormBuilderInterface $builder The form builder.
     * @param  array                $options The options for this form.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('member', EntityType::class, [
                'label' => 'production_membership.form.member',
                'class' => User::class,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'production_membership.form.roles',
                'multiple' => true,
                'required' => false,
                'choices' => [
                    'production_membership.form.roles_choices.editor' => 'GROUP_ROLE_EDITOR',
                    'production_membership.form.roles_choices.admin' => 'GROUP_ROLE_ADMIN',
                ],
            ])
            ->add('production_roles', CollectionType::class, [
                'label' => 'production_membership.form.production_roles',
                'entry_type' => ProductionRoleType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'production_membership.form.status',
                'choices' => [
                    'production_membership.form.status_choices.active' => true,
                    'production_membership.form.status_choices.blocked' => false,
                ],
            ])
            ->add('expiry', DateTimeType::class, [
                'label' => 'production_membership.form.expiry',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param  OptionsResolver $resolver The options resolver.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            'data_class' => ProductionMembership::class,
        ]);
    }
}
