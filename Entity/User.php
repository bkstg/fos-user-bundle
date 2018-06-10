<?php

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\User\UserInterface;
use Bkstg\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use MidnightLuke\GroupSecurityBundle\Model\GroupMemberInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;

class User extends BaseUser implements GroupMemberInterface, UserInterface
{

    protected $id;
    private $has_profile;
    private $memberships;
    private $first_name;
    private $last_name;
    private $height;
    private $weight;
    private $phone;
    private $facebook;
    private $twitter;
    private $image;
    private $slug;

    /**
     * Create a new instance of User.
     */
    public function __construct()
    {
        parent::__construct();
        $this->memberships = new ArrayCollection();
    }

    /**
     * Get id
     * @return
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * {@inheritdoc}
     */
    public function addMembership(GroupMembershipInterface $membership): self
    {
        if (!$membership instanceof ProductionMembership) {
            throw new MembershipTypeNotSupportedException();
        }

        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMembership(GroupMembershipInterface $membership): self
    {
        if ($this->memberships->contains($membership)) {
            $this->memberships->remove($membership);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMembership(GroupMembershipInterface $membership): bool
    {
        return $this->memberships->contains($membership);
    }

    /**
     * Set height
     *
     * @param length $height
     *
     * @return Profile
     */
    public function setHeight(Length $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return length
     */
    public function getHeight(): ?Length
    {
        return $this->height;
    }

    /**
     * Set weight
     *
     * @param mass $weight
     *
     * @return Profile
     */
    public function setWeight(Mass $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return mass
     */
    public function getWeight(): ?Mass
    {
        return $this->weight;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Profile
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     *
     * @return Profile
     */
    public function setFacebook(string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     *
     * @return Profile
     */
    public function setTwitter(string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * Set image
     *
     * @param Media $image
     *
     * @return Profile
     */
    public function setImage(?Media $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Media
     */
    public function getImage(): ?Media
    {
        return $this->image;
    }

    /**
     * Set firstName
     *
     * @param string $first_name
     *
     * @return Profile
     */
    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $last_name
     *
     * @return Profile
     */
    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Profile
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Get has_profile
     * @return bool
     */
    public function hasProfile(): bool
    {
        return ($this->has_profile === true);
    }

    /**
     * Set has_profile
     * @return $this
     */
    public function setHasProfile(bool $has_profile): self
    {
        $this->has_profile = $has_profile;
        return $this;
    }

    public function __toString()
    {
        if (!isset($this->first_name) || !isset($this->last_name)) {
            return $this->username;
        }
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }
}
