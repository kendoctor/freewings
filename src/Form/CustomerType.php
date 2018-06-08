<?php

namespace App\Form;

use App\Entity\Customer;
use App\Form\Type\CollectionExType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('images', CollectionExType::class, [
                'entry_type' => CustomerMediaType::class,
                'entry_options' => ['label'=>false],
                'by_reference' => false,
                'label' => 'customer.images'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => Customer::getAvailableTypes(),
                'choice_label' => function($choice)
                {
                    return Customer::getTypeName($choice);
                }
            ])
            ->add('description')
            ->add('mobile')
            ->add('telephone')
            ->add('fax')
            ->add('qq')
            ->add('address')
            ->add('weight')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
