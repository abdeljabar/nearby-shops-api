<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Shop;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
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
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="users")
     */
    private $preferredShops;

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

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     *
     * @return string
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        if ($plainPassword !== null) {
            $this->setPassword($plainPassword);
        }

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


}
