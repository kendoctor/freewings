<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/3/18
 * Time: 11:48 AM
 */

namespace App\Form\Type;


use App\Form\DataTransformer\TextToEntityTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputEntityType extends AbstractType
{
    private  $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'property' => null,
            'locale' => null
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addModelTransformer(new TextToEntityTransformer(
            $this->em,
            $options['class'],
            $options['property'],
            $options['locale']
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'inputToEntity_';
    }

}