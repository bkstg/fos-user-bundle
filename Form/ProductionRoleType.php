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
use Bkstg\FOSUserBundle\Entity\ProductionRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionRoleType extends AbstractType
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
            ->add('name', null, [
                'label' => 'production_role.form.name',
            ])
            ->add('designation', ChoiceType::class, [
                'label' => 'production_role.form.designation',
                'choices' => [
                    'production_role.form.designation_choices.crew' => 'Crew',
                    'production_role.form.designation_choices.crew' => 'Cast',
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
            'data_class' => ProductionRole::class,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'bkstg_production_role';
    }
}
