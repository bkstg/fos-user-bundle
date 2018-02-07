<?php

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use MidnightLuke\GroupSecurityBundle\Model\GroupMemberInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;

class User extends BaseUser implements GroupMemberInterface, UserInterface
{

    protected $id;
    private $memberships;
    private $profile;

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
    public function getId()
    {
        return $this->id;
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
    public function addMembership(GroupMembershipInterface $membership)
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
    public function removeMembership(GroupMembershipInterface $membership)
    {
        if ($this->memberships->contains($membership)) {
            $this->memberships->remove($membership);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMembership(GroupMembershipInterface $membership)
    {
        return $this->memberships->contains($membership);
    }

    public function __toString(): string
    {
        return (string) (($this->profile === null) ? $this->username : $this->getProfile()->__toString());
    }

    /**
     * Set profile
     *
     * @param Profile $profile
     *
     * @return User
     */
    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
