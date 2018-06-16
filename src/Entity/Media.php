<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $originalName;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @Vich\UploadableField(mapping="media_file", originalName="originalName", fileNameProperty="filename")
     */
    private $file;


    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="media")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostMedia", mappedBy="media")
     */
    private $postMedia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WallPaintingPhoto", mappedBy="media")
     */
    private $wallPaintingPhotos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomerMedia", mappedBy="media")
     */
    private $customerMedia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserMedia", mappedBy="media")
     */
    private $userMedia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryMedia", mappedBy="media")
     */
    private $categoryMedia;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->postMedia = new ArrayCollection();
        $this->wallPaintingPhotos = new ArrayCollection();
        $this->customerMedia = new ArrayCollection();
        $this->userMedia = new ArrayCollection();
        $this->categoryMedia = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|PostMedia[]
     */
    public function getPostMedia(): Collection
    {
        return $this->postMedia;
    }

    public function addPostMedium(PostMedia $postMedium): self
    {
        if (!$this->postMedia->contains($postMedium)) {
            $this->postMedia[] = $postMedium;
            $postMedium->setMedia($this);
        }

        return $this;
    }

    public function removePostMedium(PostMedia $postMedium): self
    {
        if ($this->postMedia->contains($postMedium)) {
            $this->postMedia->removeElement($postMedium);
            // set the owning side to null (unless already changed)
            if ($postMedium->getMedia() === $this) {
                $postMedium->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return Media
     * @throws \Exception
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return Collection|WallPaintingPhoto[]
     */
    public function getWallPaintingPhotos(): Collection
    {
        return $this->wallPaintingPhotos;
    }

    public function addWallPaintingPhoto(WallPaintingPhoto $wallPaintingPhoto): self
    {
        if (!$this->wallPaintingPhotos->contains($wallPaintingPhoto)) {
            $this->wallPaintingPhotos[] = $wallPaintingPhoto;
            $wallPaintingPhoto->setMedia($this);
        }

        return $this;
    }

    public function removeWallPaintingPhoto(WallPaintingPhoto $wallPaintingPhoto): self
    {
        if ($this->wallPaintingPhotos->contains($wallPaintingPhoto)) {
            $this->wallPaintingPhotos->removeElement($wallPaintingPhoto);
            // set the owning side to null (unless already changed)
            if ($wallPaintingPhoto->getMedia() === $this) {
                $wallPaintingPhoto->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomerMedia[]
     */
    public function getCustomerMedia(): Collection
    {
        return $this->customerMedia;
    }

    public function addCustomerMedium(CustomerMedia $customerMedium): self
    {
        if (!$this->customerMedia->contains($customerMedium)) {
            $this->customerMedia[] = $customerMedium;
            $customerMedium->setMedia($this);
        }

        return $this;
    }

    public function removeCustomerMedium(CustomerMedia $customerMedium): self
    {
        if ($this->customerMedia->contains($customerMedium)) {
            $this->customerMedia->removeElement($customerMedium);
            // set the owning side to null (unless already changed)
            if ($customerMedium->getMedia() === $this) {
                $customerMedium->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserMedia[]
     */
    public function getUserMedia(): Collection
    {
        return $this->userMedia;
    }

    public function addUserMedium(UserMedia $userMedium): self
    {
        if (!$this->userMedia->contains($userMedium)) {
            $this->userMedia[] = $userMedium;
            $userMedium->setMedia($this);
        }

        return $this;
    }

    public function removeUserMedium(UserMedia $userMedium): self
    {
        if ($this->userMedia->contains($userMedium)) {
            $this->userMedia->removeElement($userMedium);
            // set the owning side to null (unless already changed)
            if ($userMedium->getMedia() === $this) {
                $userMedium->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CategoryMedia[]
     */
    public function getCategoryMedia(): Collection
    {
        return $this->categoryMedia;
    }

    public function addCategoryMedium(CategoryMedia $categoryMedium): self
    {
        if (!$this->categoryMedia->contains($categoryMedium)) {
            $this->categoryMedia[] = $categoryMedium;
            $categoryMedium->setMedia($this);
        }

        return $this;
    }

    public function removeCategoryMedium(CategoryMedia $categoryMedium): self
    {
        if ($this->categoryMedia->contains($categoryMedium)) {
            $this->categoryMedia->removeElement($categoryMedium);
            // set the owning side to null (unless already changed)
            if ($categoryMedium->getMedia() === $this) {
                $categoryMedium->setMedia(null);
            }
        }

        return $this;
    }


}
