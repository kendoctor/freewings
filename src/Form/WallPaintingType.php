<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\WallPainting;
use App\Entity\WallPaintingArtist;
use App\Form\DataTransformer\ArrayToCollectionExTransformer;
use App\Form\Type\CollectionExType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingArtistRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('title')
            ->add('category', EntityType::class, [
                'choice_label' => 'levelName',
                'class' => Category::class,
                'choices' => $this->categoryRepository->getChoicesList(true, 'wall_painting'),
                'label' => 'wall_painting.category'

            ])
            ->add('customer')
            ->add('images', CollectionExType::class, [
                'entry_type' => PostMediaType::class,
                'entry_options' => ['label'=>false],
                'by_reference' => false,
                'label' => 'wall_painting.images'
            ])
            ->add('brief')
            ->add('content')
            ->add('weight')
            ->add('wallPaintingArtists', ChoiceType::class, [
                'choices' => $this->userRepository->getArtists(),
                'choice_label' => 'username',
                'expanded' => true,
                'multiple' => true,
                'label' => 'wall_painting.artists',
                'choice_value' => function($artist)
                {
                    return $artist->getId();
                }
            ])
            ->add('photos', CollectionExType::class, [
                'entry_type' => WallPaintPhotoType::class,
                'by_reference' => false,
                'label' => 'wall_painting.photos',
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('isPublished')
        ;
        $builder->get('wallPaintingArtists')->addModelTransformer(new ArrayToCollectionExTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WallPainting::class,
        ]);
    }
}
