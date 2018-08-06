<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Message;
use App\Form\Type\CollectionExType;
use App\Repository\CategoryRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('title', null, [
                'label' => 'message.form.title'
            ])
            ->add('category', EntityType::class, [
                'choice_label' => 'levelName',
                'class' => Category::class,
                'choices' => $this->categoryRepository->getChoicesList(true, 'message'),
                'label' => 'message.form.category'

            ])
            ->add('cover', MediaType::class, [
                'label' => 'message.form.cover'
            ])
            ->add('brief', null, [
                'label' => 'message.form.brief'
            ])
            ->add('content', CKEditorType::class, [
                'config'=> ['toolbar' => 'standard'],
                'label' => 'message.form.content'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'message.form.weight'
            ])
            ->add('isPublished', null , [
                'label' => 'message.form.isPublished'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
