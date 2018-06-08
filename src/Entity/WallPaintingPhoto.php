<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WallPaintingPhotoRepository")
 */
class WallPaintingPhoto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WallPainting", inversedBy="photos")
     */
    private $wallPainting;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Media", inversedBy="wallPaintingPhotos", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getId()
    {
        return $this->id;
    }

    public function getWallPainting(): ?WallPainting
    {
        return $this->wallPainting;
    }

    public function setWallPainting(?WallPainting $wallPainting): self
    {
        $this->wallPainting = $wallPainting;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
