<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Message;
use App\Form\Type\CollectionExType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, [
                'choice_label' => 'levelName',
                'class' => Category::class,
                'choices' => $this->categoryRepository->getChoicesList(true, 'message'),
                'label' => 'message.category'

            ])
            ->add('images', CollectionExType::class, [
                'entry_type' => PostMediaType::class,
                'entry_options' => ['label'=>false],
                'by_reference' => false,
                'label' => 'wall_painting.images'
            ])
            ->add('content')
            ->add('brief')
            ->add('weight')
            ->add('isPublished')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
