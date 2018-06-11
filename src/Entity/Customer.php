<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 */
class Customer
{
    const TYPE_PERSONAL = 'personal';
    const TYPE_ENTERPRISE = 'enterprise';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mobile;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    protected static $typeName = [
        self::TYPE_PERSONAL    => 'Personal',
        self::TYPE_ENTERPRISE  => 'Enterprise',
    ];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WallPainting", mappedBy="customer")
     */
    private $wallPaintings;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $qq;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomerMedia", mappedBy="customer", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAlbum;


    public function __construct()
    {
        $this->type = self::TYPE_PERSONAL;
        $this->wallPaintings = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->weight = 0;
        $this->isAlbum = false;
    }

    /**
     * @param  string $typeShortName
     * @return string
     */
    public static function getTypeName($typeShortName)
    {
        if (!isset(static::$typeName[$typeShortName])) {
            return "Unknown type ($typeShortName)";
        }

        return static::$typeName[$typeShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableTypes()
    {
        return [
            self::TYPE_PERSONAL,
            self::TYPE_ENTERPRISE,
        ];
    }


    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getWallPaintings(): Collection
    {
        return $this->wallPaintings;
    }

    public function addWallPainting(WallPainting $wallPainting): self
    {
        if (!$this->wallPaintings->contains($wallPainting)) {
            $this->wallPaintings[] = $wallPainting;
            $wallPainting->setCustomer($this);
        }

        return $this;
    }

    public function removeWallPainting(WallPainting $wallPainting): self
    {
        if ($this->wallPaintings->contains($wallPainting)) {
            $this->wallPaintings->removeElement($wallPainting);
            // set the owning side to null (unless already changed)
            if ($wallPainting->getCustomer() === $this) {
                $wallPainting->setCustomer(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getQq(): ?string
    {
        return $this->qq;
    }

    public function setQq(?string $qq): self
    {
        $this->qq = $qq;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Collection|CustomerMedia[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(CustomerMedia $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setCustomer($this);
        }

        return $this;
    }

    public function removeImage(CustomerMedia $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getCustomer() === $this) {
                $image->setCustomer(null);
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

    public function getImageByToken($token = 'cover')
    {
        /** @var CustomerMedia $image */
        foreach ($this->images as $image) {
            if( $image->getToken() === $token)
            {
                if($image->getMedia() === null)
                {
                    $image->setMedia(new Media());
                }

                return  $image->getMedia();
            }
        }


        $image = new CustomerMedia();
        $image->setMedia(new Media());
        $image->setToken($token);
        $this->addImage($image);
        return $image->getMedia();
    }

    public function getListImage()
    {
        return $this->getImageByToken('list');
    }

    public function getCoverImage()
    {
        return $this->getImageByToken('cover');
    }

    public function getThumbImage()
    {
        return $this->getImageByToken('thumb');
    }

    public function getIsAlbum(): ?bool
    {
        return $this->isAlbum;
    }

    public function setIsAlbum(?bool $isAlbum): self
    {
        $this->isAlbum = $isAlbum;

        return $this;
    }

}
