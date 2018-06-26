<?php

namespace Bkstg\FOSUserBundle\Form;

use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Bkstg\FOSUserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param  FormBuilderInterface $builder The form builder.
     * @param  array                $options The form options.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'user.form.username',
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.form.email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'user.form.roles',
                'multiple' => true,
                'required' => false,
                'choices' => [
                    'user.form.roles_choices.editor' => 'ROLE_EDITOR',
                    'user.form.roles_choices.admin' => 'ROLE_ADMIN',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'user.form.enabled',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            'data_class' => User::class,
        ]);
    }
}
