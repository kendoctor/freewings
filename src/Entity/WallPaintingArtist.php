<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WallPaintingArtistRepository")
 */
class WallPaintingArtist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WallPainting", inversedBy="wallPaintingArtists")
     */
    private $wallPainting;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wallPaintingArtists", cascade={"persist"})
     */
    private $artist;

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

    public function getArtist(): ?User
    {
        return $this->artist;
    }

    public function setArtist(?User $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getName()
    {
        return $this->artist->getUsername();
    }
}
