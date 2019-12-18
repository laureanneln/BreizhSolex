<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 */
class Status
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
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomerOrder", mappedBy="status")
     */
    private $customerOrders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->customerOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|CustomerOrder[]
     */
    public function getCustomerOrders(): Collection
    {
        return $this->customerOrders;
    }

    public function addCustomerOrder(CustomerOrder $customerOrder): self
    {
        if (!$this->customerOrders->contains($customerOrder)) {
            $this->customerOrders[] = $customerOrder;
            $customerOrder->setStatus($this);
        }

        return $this;
    }

    public function removeCustomerOrder(CustomerOrder $customerOrder): self
    {
        if ($this->customerOrders->contains($customerOrder)) {
            $this->customerOrders->removeElement($customerOrder);
            // set the owning side to null (unless already changed)
            if ($customerOrder->getStatus() === $this) {
                $customerOrder->setStatus(null);
            }
        }

        return $this;
    }
}
