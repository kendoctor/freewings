<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    const TYPE_NORMAL =  1;
    const TYPE_ARTIST =  4;
    const TYPE_ADMIN =  8;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;


    private $plainPassword;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="createdBy")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="createdBy", orphanRemoval=true)
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="createdBy")
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WallPaintingArtist", mappedBy="artist")
     */
    private $wallPaintingArtists;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", mappedBy="users")
     */
    private $groups;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;


    protected static $typeName = [
        self::TYPE_NORMAL => 'user.normal',
        self::TYPE_ARTIST => 'user.artist',
        self::TYPE_ADMIN => 'user.admin'
    ];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserMedia", mappedBy="user", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $positionTitle;

    public function __construct()
    {
        $this->isActive = true;
        $this->posts = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->wallPaintingArtists = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->type = self::TYPE_NORMAL;
        $this->images = new ArrayCollection();
    }


    public static function getTypeName($type)
    {
        if (!isset(static::$typeName[$type])) {
            return "Unknown type ($type)";
        }
        return static::$typeName[$type];
    }

    public static function getAvailableTypes()
    {
        return [
            self::TYPE_NORMAL,
            self::TYPE_ARTIST,
            self::TYPE_ADMIN
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
            $post->setCreatedBy($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCreatedBy() === $this) {
                $post->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getCreatedBy() === $this) {
                $category->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->username . '|'. $this->email;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->contains($medium)) {
            $this->media->removeElement($medium);
            // set the owning side to null (unless already changed)
            if ($medium->getCreatedBy() === $this) {
                $medium->setCreatedBy(null);
            }
        }

        return $this;
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
            $wallPaintingArtist->setArtist($this);
        }

        return $this;
    }

    public function removeWallPaintingArtist(WallPaintingArtist $wallPaintingArtist): self
    {
        if ($this->wallPaintingArtists->contains($wallPaintingArtist)) {
            $this->wallPaintingArtists->removeElement($wallPaintingArtist);
            // set the owning side to null (unless already changed)
            if ($wallPaintingArtist->getArtist() === $this) {
                $wallPaintingArtist->setArtist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(UserGroup $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(UserGroup $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getImageByToken($token = 'icon')
    {
        /** @var UserMedia $image */
        foreach ($this->images as $image) {
            if( $image->getToken() === $token)
                return $image->getMedia();
        }

        return null;
    }

    public function getIconImage()
    {
        return $this->getImageByToken('icon');
    }

    public function getFigureImage()
    {
        return $this->getImageByToken('figure');
    }


    public function getTranslations()
    {
        return $this->translations;
    }



    /**
     * @return Collection|UserMedia[]
     */
    public function getImages(): Collection
    {
        $tokens = ['icon', 'figure'];

        foreach ($tokens as $token) {
            if ($this->getImageByToken($token) === null) {
                $image = new UserMedia();
                $image->setToken($token);
                $this->addImage($image);
            }
        }

        return $this->images;
    }

    public function addImage(UserMedia $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUser($this);
        }

        return $this;
    }

    public function removeImage(UserMedia $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getPositionTitle(): ?string
    {
        return $this->positionTitle;
    }

    public function setPositionTitle(?string $positionTitle): self
    {
        $this->positionTitle = $positionTitle;

        return $this;
    }
}
