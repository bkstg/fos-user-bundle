<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Form;

use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Bkstg\FOSUserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
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
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The form options.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('enabled', ChoiceType::class, [
                'label' => 'user.form.status',
                'choices' => [
                    'user.form.status_choices.active' => true,
                    'user.form.status_choices.blocked' => false,
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver The options resolver.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            'data_class' => User::class,
        ]);
    }
}
