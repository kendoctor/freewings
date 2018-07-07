<?php

namespace App\Form;

use App\Entity\Customer;
use App\Form\Type\CollectionExType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'customer.form.title'
            ])
            ->add('isRecommended', CheckboxType::class, [
                'label' => 'customer.form.isRecommended'
            ])
            ->add('logo', MediaType::class, [
                'label' => 'customer.form.logo'
            ])
            ->add('cover', MediaType::class, [
                'label' => 'customer.form.cover'
            ])
//            ->add('images', CollectionExType::class, [
//                'entry_type' => CustomerMediaType::class,
//                'entry_options' => ['label'=>false],
//                'by_reference' => false,
//                'label' => 'customer.images'
//            ])
            ->add('type', ChoiceType::class, [
                'label' => 'customer.form.type',
                'choices' => Customer::getAvailableTypes(),
                'choice_label' => function($choice)
                {
                    return Customer::getTypeName($choice);
                }
            ])
            ->add('description', TextareaType::class, [
                'label' => 'customer.form.description'
            ])
            ->add('mobile', TextType::class, [
                'label' => 'customer.form.mobile'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'customer.form.telephone'
            ])
            ->add('fax', TextType::class, [
                'label' => 'customer.form.fax'
            ])
            ->add('qq', TextType::class, [
                'label' => 'customer.form.qq'
            ])
            ->add('address', TextType::class, [
                'label' => 'customer.form.address'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'customer.form.weight'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
