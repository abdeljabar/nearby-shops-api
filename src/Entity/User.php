<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Shop;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"email"}, message="This email address has already been used")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="likers")
     */
    private $preferredShops;

    /**
     * @ORM\OneToMany(targetEntity="DislikedShop", mappedBy="user")
     */
    private $dislikedShops;

    /**
     * @return array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->preferredShops = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    public function serialize() {
        return serialize([
            $this->id,
            $this->email,
            $this->password
        ]);
    }

    public function unserialize($serialized) {
        list($this->id, $this->email, $this->password) = unserialize($serialized);
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
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
        return null;
    }

    /**
     * Add/like shop
     * @param \App\Entity\Shop $shop
     * @return $this
     */
    public function addShop(Shop $shop) {
        $this->preferredShops[] = $shop;

        return $this;
    }

    /**
     * Remove/unlike shop
     * @param \App\Entity\Shop $shop
     */
    public function removeShop(Shop $shop) {
        $this->preferredShops->removeElement($shop);
    }

    /**
     * Get preferred/liked shops
     * @return Collection
     */
    public function getPreferredShops()
    {
        return $this->preferredShops;
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
