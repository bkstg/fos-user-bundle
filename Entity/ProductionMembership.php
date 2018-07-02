<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgCoreBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\User\ProductionMembershipInterface;
use Doctrine\Common\Collections\ArrayCollection;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupMemberInterface;

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

    /**
     * Create a new production membership.
     */
    public function __construct()
    {
        $this->production_roles = new ArrayCollection();
        $this->roles = [];
    }

    /**
     * Get the id.
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the group for this membership.
     *
     * @return ?GroupInterface
     */
    public function getGroup(): ?GroupInterface
    {
        return $this->group;
    }

    /**
     * Set the group for this membership.
     *
     * @param GroupInterface $group The group to set.
     *
     * @throws \Exception When the group is not a production.
     *
     * @return self
     */
    public function setGroup(GroupInterface $group): self
    {
        if (!$group instanceof Production) {
            throw new \Exception(sprintf('The group type "%s" is not supported.', get_class($group)));
        }
        $this->group = $group;

        return $this;
    }

    /**
     * Get the group member for this membership.
     *
     * @return ?GroupMemberInterface
     */
    public function getMember(): ?GroupMemberInterface
    {
        return $this->member;
    }

    /**
     * Set the member for this membership.
     *
     * @param GroupMemberInterface $member The member to set.
     *
     * @throws \Exception If the member is not a User.
     *
     * @return self
     */
    public function setMember(GroupMemberInterface $member): self
    {
        if (!$member instanceof User) {
            throw new \Exception(sprintf('The member type "%s" is not supported.', get_class($member)));
        }
        $this->member = $member;

        return $this;
    }

    /**
     * Get the roles for this membership.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // Always add the default role.
        $roles = $this->roles;
        $roles[] = self::GROUP_ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Set all the roles for this membership.
     *
     * @param array $roles An array of roles.
     *
     * @return self
     */
    public function setRoles(array $roles): self
    {
        // Reset and populate the roles.
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Add a role to this membership.
     *
     * @param string $role The role to add.
     *
     * @return self
     */
    public function addRole(string $role): self
    {
        $role = strtoupper($role);

        // Don't add the default role, it is always present.
        if (self::GROUP_ROLE_DEFAULT === $role) {
            return $this;
        }

        // Don't duplicate any roles.
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove a role from this membership.
     *
     * @param string $role The role to remove.
     *
     * @return self
     */
    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Determine if this membership has a role.
     *
     * @param string $role The role to check for.
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Get the status of this membership.
     *
     * @return ?boolean
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * Set the status of this membership.
     *
     * @param bool $status The status to set.
     *
     * @return self
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the expiry for this membership.
     *
     * @return ?\DateTimeInterface
     */
    public function getExpiry(): ?\DateTimeInterface
    {
        return $this->expiry;
    }

    /**
     * Set the expiry for this membership.
     *
     * @param ?\DateTimeInterface $expiry The expiry to set.
     *
     * @return self
     */
    public function setExpiry(?\DateTimeInterface $expiry): self
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Check if this membership is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return null !== $this->expiry && $this->expiry < new \DateTime('now');
    }

    /**
     * Add a production role to this membership.
     *
     * @param ProductionRole $production_role The production role to add.
     *
     * @return self
     */
    public function addProductionRole(ProductionRole $production_role): self
    {
        $production_role->setProductionMembership($this);
        $this->production_roles->add($production_role);

        return $this;
    }

    /**
     * Remove a production role from this membership.
     *
     * @param ProductionRole $production_role The production role to remove.
     *
     * @return self
     */
    public function removeProductionRole(ProductionRole $production_role): self
    {
        $this->production_roles->removeElement($production_role);

        return $this;
    }

    /**
     * Get the production roles.
     *
     * @return ArrayCollection
     */
    public function getProductionRoles()
    {
        return $this->production_roles;
    }
}
