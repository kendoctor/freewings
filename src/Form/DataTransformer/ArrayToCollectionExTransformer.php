<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/21/18
 * Time: 6:01 AM
 */

namespace App\Form\DataTransformer;


use App\Entity\WallPaintingArtist;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToCollectionExTransformer implements DataTransformerInterface
{


    public function transform($entities)
    {
        $artists = [];
        //return $entities;
        foreach($entities as $entity)
        {
            $artists[] = $entity->getArtist();
        }
        return $artists;
    }


    public function reverseTransform($artists)
    {
        $wallPaintingArtists = [];
        foreach ($artists as $artist) {
            $wallPaintingArtist = new WallPaintingArtist();
            $wallPaintingArtist->setArtist($artist);
            $wallPaintingArtists[] = $wallPaintingArtist;
        }
        return $wallPaintingArtists;
    }
}