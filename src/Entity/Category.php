<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="App\Entity\CategoryTranslation")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @Gedmo\Locale
     */
    private $locale;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"lft" = "ASC" })
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="category")
     */
    private $posts;


    /**
     * @ORM\OneToMany(
     *   targetEntity="App\Entity\CategoryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryMedia", mappedBy="category", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRecommended;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", cascade={"persist", "remove"})
     */
    private $icon;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=128)
     */
    private $token;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oldNewsCategoryId;


    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->weight = 0;
        $this->isRecommended = false;
    }

    public function isRoot()
    {
        return $this === $this->getRoot();
    }

    public function getId()
    {
        return $this->id;
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

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     */
    public function setRoot($root): void
    {
        $this->root = $root;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Post[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param TagTranslation $translation
     */
    public function addTranslation(TagTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Category
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }


    public function __toString()
    {
        return $this->getName();
    }


    public function getLevelName()
    {
        return str_repeat('-', $this->getLvl()) . $this->getName();
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

    /**
     * @return Collection|CategoryMedia[]
     */
    public function getImages(): Collection
    {
        $tokens = ['icon', 'thumb', 'list'];

        foreach ($tokens as $token) {

            if($this->getImageByToken($token) === null) {
                $image = new CategoryMedia();
                $image->setMedia(new Media());
                $image->setToken($token);
                $this->addImage($image);
            }
        }

        return $this->images;
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

    public function getThumbImage()
    {
        return $this->getImageByToken('thumb');
    }

    public function addImage(CategoryMedia $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setCategory($this);
        }

        return $this;
    }

    public function removeImage(CategoryMedia $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getCategory() === $this) {
                $image->setCategory(null);
            }
        }

        return $this;
    }

    public function getIsRecommended(): ?bool
    {
        return $this->isRecommended;
    }

    public function setIsRecommended(bool $isRecommended): self
    {
        $this->isRecommended = $isRecommended;

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

    public function getIcon(): ?Media
    {
        return $this->icon;
    }

    public function setIcon(?Media $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {

        $this->token = $token;

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

    public function getOldNewsCategoryId(): ?int
    {
        return $this->oldNewsCategoryId;
    }

    public function setOldNewsCategoryId(?int $oldNewsCategoryId): self
    {
        $this->oldNewsCategoryId = $oldNewsCategoryId;

        return $this;
    }

}
