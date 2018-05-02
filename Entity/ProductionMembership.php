<?php

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\Exception\UserHasNoRoleException;
use Bkstg\CoreBundle\Exception\UserHasRoleException;
use Bkstg\CoreBundle\User\ProductionMembershipInterface;
use Bkstg\FOSUserBundle\Entity\ProductionRole;
use Bkstg\FOSUserBundle\Entity\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMemberInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;

class ProductionMembership implements ProductionMembershipInterface
{
    const GROUP_ROLE_DEFAULT = 'GROUP_ROLE_USER';

    private $id;
    private $group;
    private $member;
    private $roles;
    private $status;
    private $expiry;
    private $production_roles;
    private $profile;

    public function __construct()
    {
        $this->production_roles = new ArrayCollection();
        $this->roles = [];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(GroupInterface $group)
    {
        if (!$group instanceof Production) {
            throw new GroupTypeNotSupportedException();
        }
        $this->group = $group;
        return $this;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setMember(GroupMemberInterface $member)
    {
        if (!$member instanceof User) {
            throw new MemberTypeNotSupportedException();
        }
        $this->member = $member;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = self::GROUP_ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role)
    {
        $role = strtoupper($role);
        if ($role === self::GROUP_ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        if (!in_array($status, [
            GroupMembershipInterface::STATUS_ACTIVE,
            GroupMembershipInterface::STATUS_BLOCKED,
        ])) {
            throw new InvalidStatusException();
        }
        $this->status = $status;
        return $this;
    }

    public function getExpiry()
    {
        return $this->expiry;
    }

    public function setExpiry(\DateTime $expiry = null)
    {
        $this->expiry = $expiry;
        return $this;
    }

    public function isExpired()
    {
        // No expiry on this membership.
        if ($this->expiry === null) {
            return false;
        }

        $now = new \DateTime();
        return ($now < $this->expiry);
    }

    public function isActive()
    {
        if ($this->isExpired()) {
            return false;
        }
        return ($this->status == GroupMembershipInterface::STATUS_ACTIVE);
    }

    public function addProductionRole(ProductionRole $production_role)
    {
        $production_role->setProductionMembership($this);
        $this->production_roles->add($production_role);
    }

    public function removeProductionRole(ProductionRole $production_role)
    {
        $this->production_roles->removeElement($production_role);
    }

    public function getProductionRoles()
    {
        return $this->production_roles;
    }

    /**
     * Get profile
     * @return
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profile
     * @return $this
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
        return $this;
    }
}
