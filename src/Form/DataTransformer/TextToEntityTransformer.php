<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/3/18
 * Time: 11:51 AM
 */

namespace App\Form\DataTransformer;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TextToEntityTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $className;
    private $property;
    private $locale;

    public function __construct(EntityManagerInterface $entityManager, $className, $property, $locale = null)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
        $this->property = $property;
        $this->locale = $locale;
    }

    public function transform($entity)
    {
        if(is_object($entity))
        {
           return $entity->getName();
        }

        return "";
    }


    public function reverseTransform($text)
    {
        //if disable new entity will throw an exception for validation
        $repository = $this->entityManager->getRepository($this->className);
        $entity = $repository->findOneBy([$this->property => $text]);

        if($entity === null)
        {
            $entity = new $this->className;
            $setter = "set" . ucfirst(strtolower($this->property));
            $entity->$setter($text);
            if($this->locale)
            {
                $entity->setTranslatableLocale($this->locale);
            }
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $entity;
    }

}