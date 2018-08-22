<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\WallPainting;
use App\Entity\WallPaintingArtist;
use App\Form\DataTransformer\ArrayToCollectionExTransformer;
use App\Form\DataTransformer\IntegerToBitsTransformer;
use App\Form\Type\CollectionExType;
use App\Form\Type\Select2Type;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingArtistRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallPaintingType extends AbstractType
{
    private $categoryRepository;
    private $wallPaintingArtistRepository;
    private $userRepository;

    public function __construct(UserRepository $userRepository, WallPaintingArtistRepository $wallPaintingArtistRepository, CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->wallPaintingArtistRepository = $wallPaintingArtistRepository;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $id = $data->getId();
        $wallPaintingArtistRepository = $this->wallPaintingArtistRepository;

        $builder
            ->add('title', TextType::class, [
                'label' => 'wall_painting.form.title'
            ])
            ->add('category', EntityType::class, [
                'choice_label' => 'levelName',
                'class' => Category::class,
                'choices' => $this->categoryRepository->getChoicesList(true, 'wall_painting'),
                'label' => 'wall_painting.form.category'

            ])
            ->add('customer', null, [
                'label' => 'wall_painting.form.customer'
            ])
            ->add('cover', MediaType::class, [
                'label' => 'wall_painting.form.cover'
            ])
//            ->add('images', CollectionExType::class, [
//                'entry_type' => PostMediaType::class,
//                'entry_options' => ['label'=>false],
//                'by_reference' => false,
//                'label' => 'wall_painting.images'
//            ])
            ->add('brief', TextareaType::class, [
                'label' => 'wall_painting.form.brief'
            ])
            ->add('content', CKEditorType::class, [
                'config'=> ['toolbar' => 'standard'],
                'label' => 'wall_painting.form.content'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'wall_painting.form.weight'
            ])
            ->add('postTags', Select2Type::class, [
                'label' => 'wall_painting.form.tags'
            ])
            ->add('wallPaintingArtists', ChoiceType::class, [
                'choices' => $this->userRepository->getArtists(),
                'choice_label' => 'username',
                'expanded' => true,
                'multiple' => true,
                'label' => 'wall_painting.form.artists',
                'choice_value' => function($artist)
                {
                    return $artist->getId();
                }
            ])
            ->add('recommendType', ChoiceType::class, [
                'label' => 'wall_painting.form.recommendType',
                'expanded' => true,
                'multiple' => true,
                'choices' => Post::getAvailableRecommendTypes(),
                'choice_label' => function($choice)
                {
                    return Post::getRecommendTypeName($choice);
                }

            ])
            ->add('isPublished', null, [
                'label' => 'wall_painting.form.isPublished'
            ])
        ;
        $builder->get('wallPaintingArtists')->addModelTransformer(new ArrayToCollectionExTransformer());
        $builder->get('recommendType')->addModelTransformer(new IntegerToBitsTransformer(Post::getAvailableRecommendTypes()));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WallPainting::class,
        ]);
    }
}
