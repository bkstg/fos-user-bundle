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
     * Get id.
     *
     * @return ?integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get email.
     *
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     *
     * @return ArrayCollection
     */
    public function getMemberships(): ArrayCollection
    {
        return $this->memberships;
    }

    /**
     * {@inheritdoc}
     *
     * @param  GroupMembershipInterface $membership The membership to set.
     * @throws \Exception If the membership is not a production membership.
     * @return self
     */
    public function addMembership(GroupMembershipInterface $membership): self
    {
        if (!$membership instanceof ProductionMembership) {
            throw new \Exception(sprintf('The membership type "%s" is not supported.', get_class($membership)));
        }

        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  GroupMembershipInterface $membership The membership to remove.
     * @return self
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
     *
     * @param  GroupMembershipInterface $membership The membership to check for.
     * @return boolean
     */
    public function hasMembership(GroupMembershipInterface $membership): bool
    {
        return $this->memberships->contains($membership);
    }

    /**
     * Set height.
     *
     * @param  ?Length $height The height to set.
     * @return self
     */
    public function setHeight(?Length $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height.
     *
     * @return ?Length
     */
    public function getHeight(): ?Length
    {
        return $this->height;
    }

    /**
     * Set weight.
     *
     * @param  ?Mass $weight The weight to set.
     * @return self
     */
    public function setWeight(?Mass $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return ?Mass
     */
    public function getWeight(): ?Mass
    {
        return $this->weight;
    }

    /**
     * Set phone.
     *
     * @param  ?string $phone The phone number.
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return ?string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set facebook.
     *
     * @param ?string $facebook The facebook url.
     * @return self
     */
    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook.
     *
     * @return ?string
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param ?string $twitter The twitter url.
     * @return self
     */
    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter.
     *
     * @return ?string
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * Set image.
     *
     * @param  ?Media $image The media object to set as the image.
     * @return self
     */
    public function setImage(?Media $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return ?Media
     */
    public function getImage(): ?Media
    {
        return $this->image;
    }

    /**
     * Set first name.
     *
     * @param  string $first_name The first name.
     * @return self
     */
    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get first name.
     *
     * @return ?string
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * Set last name.
     *
     * @param  string $last_name The last name.
     * @return self
     */
    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get last name.
     *
     * @return ?string
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * Set slug.
     *
     * @param  string $slug The slug to set.
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return ?string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Check if the user has a profile.
     *
     * @return boolean
     */
    public function hasProfile(): bool
    {
        return ($this->has_profile === true);
    }

    /**
     * Set whether the user has a profile.
     *
     * @param  boolean $has_profile Whether or not the user has a profile.
     * @return self
     */
    public function setHasProfile(bool $has_profile): self
    {
        $this->has_profile = $has_profile;
        return $this;
    }

    /**
     * Convert to a string, prefer real name, fallback on username.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (!isset($this->first_name) || !isset($this->last_name)) {
            return $this->username;
        }
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }
}
