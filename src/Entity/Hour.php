<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HourRepository")
 */
class Hour
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
    private $day_name;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $opening_hour;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $closing_hour;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayName(): ?string
    {
        return $this->day_name;
    }

    public function setDayName(string $day_name): self
    {
        $this->day_name = $day_name;

        return $this;
    }

    public function getOpeningHour(): ?\DateTimeInterface
    {
        return $this->opening_hour;
    }

    public function setOpeningHour(?\DateTimeInterface $opening_hour): self
    {
        $this->opening_hour = $opening_hour;

        return $this;
    }

    public function getClosingHour(): ?\DateTimeInterface
    {
        return $this->closing_hour;
    }

    public function setClosingHour(?\DateTimeInterface $closing_hour): self
    {
        $this->closing_hour = $closing_hour;

        return $this;
    }
}
