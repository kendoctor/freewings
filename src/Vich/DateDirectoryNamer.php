<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/9/18
 * Time: 7:52 PM
 */

namespace App\Vich;


use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DateDirectoryNamer implements DirectoryNamerInterface
{

    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param object $object The object the upload is attached to
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object
     *
     * @return string The directory name
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return $object->getCreatedAt()->format('Y-m-d');
    }
}