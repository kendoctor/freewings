<?php

namespace App\Form;

use App\Entity\Resume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'resume.form.title'
            ])
            ->add('brief', null, [
                'label' => 'resume.form.brief'
            ])
            ->add('city', null, [
                'label' => 'resume.form.city'
            ])
            ->add('content', null, [
                'label' => 'resume.form.content'
            ])
            ->add('contact', null, [
                'label' => 'resume.form.contact'
            ])
            ->add('isPublished', null, [
                'label' => 'resume.form.isPublished'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}
