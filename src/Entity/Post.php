<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"post" = "Post", "wall_painting" = "WallPainting", "message"="Message"})
 * @Gedmo\TranslationEntity(class="App\Entity\PostTranslation")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    const POST_TYPE_POST = 'post';
    const POST_TYPE_NEWS = 'news';
    const POST_TYPE_LINK = 'link';
    const POST_TYPE_WALL_PAINTING = 'wall_painting';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

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
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titlePicture;

    /**
     * @Assert\Image
     */
    private $titlePictureFile;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    private $brief;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostTag", mappedBy="post", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $postTags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostMedia", mappedBy="post", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @ORM\OneToMany(
     *   targetEntity="App\Entity\PostTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->weight = 0;
        $this->brief = "";

        $this->postTags = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->translations = new ArrayCollection();

    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getTitlePicture() :?string
    {
        return $this->titlePicture;
    }

    public function setTitlePicture(string $titlePicture): self
    {
        $this->titlePicture = $titlePicture;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitlePictureFile() : ? File
    {
        return $this->titlePictureFile;
    }


    /**
     * @param File $titlePictureFile
     * @return Post
     */
    public function setTitlePictureFile(File $titlePictureFile = null): self
    {
        $this->titlePictureFile = $titlePictureFile;

        if($titlePictureFile)
        {
            $this->updatedAt = new \DateTime();
        }

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function saveTitlePictureFile()
    {


    }

    public function removeTitlePictureFile()
    {

    }

    public function getBrief(): ?string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): self
    {
        $this->brief = $brief;

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
     * @return Collection|PostTag[]
     */
    public function getPostTags(): Collection
    {
        return $this->postTags;
    }

    public function addPostTag(PostTag $postTag): self
    {
        if (!$this->postTags->contains($postTag)) {
            $this->postTags[] = $postTag;
            $postTag->setPost($this);
        }

        return $this;
    }

    public function removePostTag(PostTag $postTag): self
    {
        if ($this->postTags->contains($postTag)) {
            $this->postTags->removeElement($postTag);
            // set the owning side to null (unless already changed)
            if ($postTag->getPost() === $this) {
                $postTag->setPost(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|PostMedia[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PostMedia $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(PostMedia $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getImageByToken($token = 'cover')
    {
        /** @var PostMedia $image */
        foreach ($this->images as $image) {
            if( $image->getToken() === $token)
                return $image->getMedia();
        }

        return null;
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

    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param TagTranslation $translation
     */
    public function addTranslation(PostTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }
    }


}
