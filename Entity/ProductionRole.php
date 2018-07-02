<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgCoreBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\User\ProductionRoleInterface;

class ProductionRole implements ProductionRoleInterface
{
    private $id;
    private $name;
    private $designation;
    private $production_membership;

    /**
     * Get the id for this role.
     *
     * @return int The id for this role.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string The name of the role.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name The name of the role.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get designation.
     *
     * @return string The designation for this role.
     */
    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    /**
     * Set designation.
     *
     * @param string $designation The designation for this role.
     *
     * @return self
     */
    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get production membership for this role.
     *
     * @return ?ProductionMembership
     */
    public function getProductionMembership(): ?ProductionMembership
    {
        return $this->production_membership;
    }

    /**
     * Set production membership for this role.
     *
     * @param ProductionMembership $production_membership The membership to set.
     *
     * @return self
     */
    public function setProductionMembership(ProductionMembership $production_membership): self
    {
        $this->production_membership = $production_membership;

        return $this;
    }
}
