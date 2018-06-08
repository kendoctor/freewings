<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/30/18
 * Time: 7:40 AM
 */

namespace App\Form\Type;


use App\Form\DataTransformer\ArrayToCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2Type extends AbstractType
{
    private $transformer;

    public function __construct(ArrayToCollectionTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'multiple' => true,
            'required' => false,
            'allow_extra_fields' => true,

        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->resetViewTransformers()
            ->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

//    public function finishView(FormView $view, FormInterface $form, array $options)
//    {
//        parent::finishView($view, $form, $options);
//        $view->vars['multiple'] = $options['multiple'];
//        if ($options['multiple']) {
//            $view->vars['full_name'] .= '[]';
//        }
//    }



}