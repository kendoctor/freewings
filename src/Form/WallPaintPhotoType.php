<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/11/18
 * Time: 9:06 AM
 */

namespace App\Form;


use App\Entity\WallPaintingPhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallPaintPhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', MediaType::class)
            ->add('description')
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WallPaintingPhoto::class,
        ]);
    }
}