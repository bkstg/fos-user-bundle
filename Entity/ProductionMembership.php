<?php

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\Exception\UserHasNoRoleException;
use Bkstg\CoreBundle\Exception\UserHasRoleException;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMemberInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;

class ProductionMembership implements GroupMembershipInterface
{
    const GROUP_ROLE_DEFAULT = 'GROUP_ROLE_USER';

    private $id;
    private $group;
    private $member;
    private $roles;
    private $status;
    private $expiry;

    public function __construct()
    {
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

    public function setRoles($roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                $this->addRole($role);
            }
        }

        return $this;
    }

    public function addRole(string $role)
    {
        if ($this->hasRole($role) || $role == self::GROUP_ROLE_DEFAULT) {
            return;
        }
        $this->roles[] = $role;
        return $this;
    }

    public function removeRole(string $role)
    {
        if (!$this->hasRole($role)) {
            return;
        }
        unset($this->roles[array_search($role, $this->roles)]);
        return $this;
    }

    public function hasRole(string $role)
    {
        if ($role == self::GROUP_ROLE_DEFAULT) {
            return true;
        }

        return in_array($role, $this->roles);
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
}
