<?php

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\IntegerToBitsTransformer;
use App\Form\Type\CollectionExType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('nickname')
            ->add('positionTitle')
            ->add('email')
            ->add('type', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => User::getAvailableTypes(),
                'choice_label' => function($choice)
                {
                    return User::getTypeName($choice);
                }

            ])
            ->add('images', CollectionExType::class, [
                'entry_type' => UserMediaType::class,
                'entry_options' => ['label'=>false],
                'by_reference' => false,
                'label' => 'user.images'
            ])
            ->add('description')
            ->add('isActive')
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
