<?php

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\IntegerToBitsTransformer;
use App\Form\Type\CollectionExType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'user.form.username'
            ])
            ->add('figure', MediaType::class, [
                'label' => 'user.form.figure'
            ])
            ->add('nickname', null, [
                'label' => 'user.form.nickname'
            ])
            ->add('positionTitle', null, [
                'label' => 'user.form.positionTitle'
            ])
            ->add('email', null, [
                'label' => 'user.form.email'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'user.form.type',
                'expanded' => true,
                'multiple' => true,
                'choices' => User::getAvailableTypes(),
                'choice_label' => function($choice)
                {
                    return User::getTypeName($choice);
                }

            ])
            ->add('description', null, [
                'label' => 'user.form.description'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'user.form.weight'
            ])
            ->add('isActive', null, [
                'label' => 'user.form.isActive'
            ])
        ;

        $builder->get('type')->addModelTransformer(new IntegerToBitsTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
