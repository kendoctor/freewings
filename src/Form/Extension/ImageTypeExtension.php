<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/27/18
 * Time: 7:20 PM
 */

namespace App\Form\Extension;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageTypeExtension extends AbstractTypeExtension
{

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['upload_field']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['upload_field'])) {
            $parentData = $form->getParent()->getData();

            //$imageFile = $entity;
            //if(null !== )
            $view->vars['media_entity'] = $form->getParent()->getData();
            $view->vars['upload_field'] = $options['upload_field'];
        }
    }
}