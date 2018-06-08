<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * @ORM\Table(uniqueConstraints={ @ORM\UniqueConstraint(name="lookup_unique_idx", columns={ "locale", "object_id", "field" }) })
 * @ORM\Entity(repositoryClass="App\Repository\TagTranslationRepository")
 */
class TagTranslation extends AbstractPersonalTranslation
{

    /**
     * Convenient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale, $field, $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;


}
