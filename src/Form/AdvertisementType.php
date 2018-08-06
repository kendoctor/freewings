<?php

namespace App\Form;

use App\Entity\Advertisement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'advertisement.form.title'
            ])
            ->add('link', null, [
                'label' => 'advertisement.form.link'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'advertisement.form.weight'
            ])
            ->add('cover', MediaType::class, [
                'label' => 'advertisement.form.cover'
            ])
            ->add('isPublished', null, [
                'label' => 'advertisement.form.isPublished'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advertisement::class,
        ]);
    }
}
