<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PreferenceRepository")
 */
class Preference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $min_stock;

    /**
     * @ORM\Column(type="integer")
     */
    private $taxe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinStock(): ?int
    {
        return $this->min_stock;
    }

    public function setMinStock(int $min_stock): self
    {
        $this->min_stock = $min_stock;

        return $this;
    }

    public function getTaxe(): ?int
    {
        return $this->taxe;
    }

    public function setTaxe(int $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }
}
