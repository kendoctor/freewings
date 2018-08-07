<?php

namespace App\Form;

use App\Entity\Branch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'branch.form.title'
            ])
            ->add('address', null, [
                'label' => 'branch.form.address'
            ])
            ->add('phone', null, [
                'label' => 'branch.form.phone'
            ])
            ->add('mobile', null, [
                'label' => 'branch.form.mobile'
            ])
            ->add('qq', null, [
                'label' => 'branch.form.qq'
            ])
            ->add('email', null, [
                'label' => 'branch.form.email'
            ])
            ->add('map', MediaType::class, [
                'label' => 'branch.form.map'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Branch::class,
        ]);
    }
}
