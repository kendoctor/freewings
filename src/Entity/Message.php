<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message extends Post
{

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     */
    private $cover;


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

    public function getCover(): ?Media
    {
        return $this->cover;
    }

    public function setCover(?Media $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
