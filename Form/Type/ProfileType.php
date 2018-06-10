<?php

namespace Bkstg\FOSUserBundle\Form\Type;

use MidnightLuke\PhpUnitsOfMeasureBundle\Form\Type\LengthType;
use MidnightLuke\PhpUnitsOfMeasureBundle\Form\Type\MassType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ])
            ->add('first_name')
            ->add('last_name')
            ->add('email', EmailType::class)
            ->add('phone')
            ->add('height', LengthType::class, [
                'required' => false,
            ])
            ->add('weight', MassType::class, [
                'required' => false,
            ])
            ->add('facebook', UrlType::class, [
                'required' => false,
            ])
            ->add('twitter', UrlType::class, [
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bkstg\FOSUserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bkstg_corebundle_profile';
    }
}
