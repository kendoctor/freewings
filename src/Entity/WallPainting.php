<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WallPaintingRepository")
 */
class WallPainting extends Post
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WallPaintingArtist", mappedBy="wallPainting", cascade={"persist","remove"})
     */
    private $wallPaintingArtists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WallPaintingPhoto", mappedBy="wallPainting", cascade={"persist","remove"})
     */
    private $photos;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="wallPaintings")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;



    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    public function __construct()
    {
        parent::__construct();
        $this->wallPaintingArtists = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->isPublished = false;
    }

    /**
     * @return Collection|WallPaintingArtist[]
     */
    public function getWallPaintingArtists(): Collection
    {
        return $this->wallPaintingArtists;
    }

    public function addWallPaintingArtist(WallPaintingArtist $wallPaintingArtist): self
    {
        if (!$this->wallPaintingArtists->contains($wallPaintingArtist)) {
            $this->wallPaintingArtists[] = $wallPaintingArtist;
            $wallPaintingArtist->setWallPainting($this);
        }

        return $this;
    }

    public function removeWallPaintingArtist(WallPaintingArtist $wallPaintingArtist): self
    {
        if ($this->wallPaintingArtists->contains($wallPaintingArtist)) {
            $this->wallPaintingArtists->removeElement($wallPaintingArtist);
            // set the owning side to null (unless already changed)
            if ($wallPaintingArtist->getWallPainting() === $this) {
                $wallPaintingArtist->setWallPainting(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WallPaintingPhoto[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(WallPaintingPhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setWallPainting($this);
        }

        return $this;
    }

    public function removePhoto(WallPaintingPhoto $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getWallPainting() === $this) {
                $photo->setWallPainting(null);
            }
        }

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): self
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function hasTag($name)
    {
        foreach ($this->getPostTags() as $postTag) {
            if($postTag->getTag()->getName() == $name)
                return true;
        }
        return false;
    }


}
