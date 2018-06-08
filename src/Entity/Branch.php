<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BranchRepository")
 */
class Branch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $qq1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $qq2;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $qq3;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email;

    public function getId()
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    public function getQq1(): ?string
    {
        return $this->qq1;
    }

    public function setQq1(string $qq1): self
    {
        $this->qq1 = $qq1;

        return $this;
    }

    public function getQq2(): ?string
    {
        return $this->qq2;
    }

    public function setQq2(string $qq2): self
    {
        $this->qq2 = $qq2;

        return $this;
    }

    public function getQq3(): ?string
    {
        return $this->qq3;
    }

    public function setQq3(?string $qq3): self
    {
        $this->qq3 = $qq3;

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
}
