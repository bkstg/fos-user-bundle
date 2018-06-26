<?php

namespace Bkstg\FOSUserBundle\Form;

use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
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
     *
     * @param  FormBuilderInterface $builder The form builder.
     * @param  array                $options The form options.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', MediaType::class, [
                'label' => 'profile.form.image',
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ])
            ->add('first_name', null, [
                'label' => 'profile.form.first_name',
            ])
            ->add('last_name', null, [
                'label' => 'profile.form.last_name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'profile.form.email',
            ])
            ->add('phone', null, [
                'label' => 'profile.form.phone',
            ])
            ->add('height', LengthType::class, [
                'label' => 'profile.form.height',
                'required' => false,
            ])
            ->add('weight', MassType::class, [
                'label' => 'profile.form.weight',
                'required' => false,
            ])
            ->add('facebook', UrlType::class, [
                'label' => 'profile.form.facebook',
                'required' => false,
            ])
            ->add('twitter', UrlType::class, [
                'label' => 'profile.form.twitter',
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            'data_class' => 'Bkstg\FOSUserBundle\Entity\User'
        ]);
    }
}
