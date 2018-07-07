<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryMedia;
use App\Entity\Tag;
use App\Form\Type\CollectionExType;
use App\Form\Type\InputEntityType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'category.form.name'
            ])
            ->add('token', null, [
                'label' => 'category.form.token',
                'required' => false
            ])
            ->add('isRecommended', CheckboxType::class, [
                'label' => 'category.form.is_recommended',
                'required' => false
            ])
            ->add('icon', MediaType::class, [
                'label' => 'category.form.icon'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'category.form.weight'
            ])

//            ->add('images', CollectionExType::class, [
//                'entry_type' => CategoryMediaType::class,
//                'entry_options' => ['label'=>false],
//                'by_reference' => false,
//                'label' => 'category.images'
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }
}
