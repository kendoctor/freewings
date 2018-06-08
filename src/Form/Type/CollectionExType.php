<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/7/18
 * Time: 3:54 AM
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CollectionExType extends AbstractType
{



    public function getParent()
    {
        return CollectionType::class;
    }

}