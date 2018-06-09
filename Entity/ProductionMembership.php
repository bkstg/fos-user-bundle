<?php declare(strict_types=1);

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
    const GROUP_ROLE_PREFIX = 'GROUP_ROLE_';
    const GROUP_ROLE_DEFAULT = 'GROUP_ROLE_USER';

    private $id;
    private $group;
    private $member;
    private $roles;
    private $status;
    private $expiry;
    private $production_roles;

    public function __construct()
    {
        $this->production_roles = new ArrayCollection();
        $this->roles = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?GroupInterface
    {
        return $this->group;
    }

    public function setGroup(GroupInterface $group): self
    {
        if (!$group instanceof Production) {
            throw new GroupTypeNotSupportedException();
        }
        $this->group = $group;
        return $this;
    }

    public function getMember(): ?GroupMemberInterface
    {
        return $this->member;
    }

    public function setMember(GroupMemberInterface $member): self
    {
        if (!$member instanceof User) {
            throw new MemberTypeNotSupportedException();
        }
        $this->member = $member;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::GROUP_ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): self
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

    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getExpiry(): ?\DateTimeInterface
    {
        return $this->expiry;
    }

    public function setExpiry(?\DateTimeInterface $expiry): self
    {
        $this->expiry = $expiry;
        return $this;
    }

    public function isExpired(): bool
    {
        // No expiry on this membership.
        if ($this->expiry === null) {
            return false;
        }

        $now = new \DateTime();
        return ($now < $this->expiry);
    }

    public function addProductionRole(ProductionRole $production_role): self
    {
        $production_role->setProductionMembership($this);
        $this->production_roles->add($production_role);
        return $this;
    }

    public function removeProductionRole(ProductionRole $production_role): self
    {
        $this->production_roles->removeElement($production_role);
        return $this;
    }

    public function getProductionRoles()
    {
        return $this->production_roles;
    }
}
