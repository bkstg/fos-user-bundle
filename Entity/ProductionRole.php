<?php declare(strict_types=1);

namespace Bkstg\FOSUserBundle\Entity;

use Bkstg\CoreBundle\User\ProductionRoleInterface;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;

class ProductionRole implements ProductionRoleInterface
{
    private $id;
    private $name;
    private $designation;
    private $production_membership;

    /**
     * Get the id for this role.
     *
     * @return integer The id for this role.
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
     * @return ProductionRole
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
     * @return ProductionRole
     */
    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;
        return $this;
    }

    /**
    * Get production_membership
    * @return
    */
    public function getProductionMembership(): ?ProductionMembership
    {
        return $this->production_membership;
    }

    /**
    * Set production_membership
    * @return $this
    */
    public function setProductionMembership(ProductionMembership $production_membership): self
    {
        $this->production_membership = $production_membership;
        return $this;
    }
}
