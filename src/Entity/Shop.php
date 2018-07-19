<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 * @ORM\Table(name="shops")
 */
class Shop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="preferredShops")
     * @ORM\JoinTable(name="preferred_shops")
     */
    private $likers;

    /**
     * @ORM\OneToMany(targetEntity="DislikedShop", mappedBy="shop")
     */
    private $dislikedShops;

    /**
     * Shop constructor.
     */
    public function __construct()
    {
        $this->likers = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @param \App\Entity\User $user
     * @return $this
     */
    public function addLiker(User $user) {
        $this->likers[] = $user;

        return $this;
    }

    /**
     * @param \App\Entity\User $user
     */
    public function removeLiker(User $user) {
        $this->likers->removeElement($user);
    }

    /**
     * @return Collection
     */
    public function getLikers()
    {
        return $this->likers;
    }

    /**
     * Add/like shop
     * @param \App\Entity\DislikedShop $shop
     * @return $this
     */
    public function addDislikedShop(DislikedShop $shop) {
        $this->dislikedShops[] = $shop;

        return $this;
    }

    /**
     * Remove from disliked shops list
     * @param \App\Entity\DislikedShop $shop
     */
    public function removeShopDislikedShop(DislikedShop $shop) {
        $this->dislikedShops->removeElement($shop);
    }

    /**
     * Get disliked shops
     * @return Collection
     */
    public function getDislikedShops()
    {
        return $this->dislikedShops;
    }

}
