<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\Type\CollectionExType;
use App\Form\Type\Select2Type;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'choice_label' => 'levelName',
                'class' => Category::class,
                'choices' => $this->categoryRepository->getChoicesList(true, 'post'),
                'label' => 'post.category'

            ])
            ->add('title', TextType::class, [
                'label' => 'post.title'
            ])
            ->add('brief', TextareaType::class, [
                'label' => 'post.brief'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'post.content'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'post.weight'
            ])
            ->add('postTags', Select2Type::class, [
                'label' => 'post.tags'
            ])
            ->add('images', CollectionExType::class, [
                'entry_type' => PostMediaType::class,
                'entry_options' => ['label'=>false],
                'by_reference' => false,
                'label' => 'post.images'
            ])

        ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
