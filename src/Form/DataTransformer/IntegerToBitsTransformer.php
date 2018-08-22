<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/19/18
 * Time: 7:29 PM
 */

namespace App\Form\DataTransformer;


use App\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IntegerToBitsTransformer implements DataTransformerInterface
{

    private $availableTypes;

    public function __construct($availableTypes)
    {
        $this->availableTypes = $availableTypes;
    }

    public function transform($intValue)
    {
        $bits = [];
        foreach($this->availableTypes as $bitType)
        {
            if($bitType & $intValue)
                $bits[] = $bitType;
        }

        return $bits;
    }


    public function reverseTransform($bits)
    {
        $intValue = 0;
        foreach ($bits as $bit) {
            $intValue |= $bit;
        }

        return $intValue;
    }
}