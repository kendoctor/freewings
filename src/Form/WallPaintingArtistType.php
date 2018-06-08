<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/10/18
 * Time: 12:03 PM
 */

namespace App\Form;


use App\Entity\WallPaintingArtist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallPaintingArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artist')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WallPaintingArtist::class,
        ]);
    }

}