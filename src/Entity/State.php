<?php


namespace App\Entity;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity()
 */
class State
{
    /**
     * @Column(type="integer")
     * @GeneratedValue()
     * @Id()
     */
    public int $id;

    /**
     * @Column()
     */
    public string $name;

    /**
     * @Column(length=2)
     */
    public string $uf;

    /**
     * @Column(type="datetime")
     */
    public \DateTime $date;

    /**
     * @ManyToOne(targetEntity="Region")
     * @JoinColumn(name="region_id", referencedColumnName="id")
     */
    public Region $region;


    /**
     * @Column(type="integer")
     */
    public int $quantityDeaths;

    /**
     * @Column(type="integer")
     */
    public int $quantityConfirmed;

    public function __construct(string $name, string $uf, Region $region)
    {
        $this->name = $name;
        $this->region = $region;
        $this->uf = $uf;

    }

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getUf(): string
    {
        return $this->uf;
    }
    public function setUf(string $uf): void
    {
        $this->uf = $uf;
    }


    public function getRegion(): Region
    {
        return $this->region;
    }
    public function setRegion(Region $region): void
    {
        $this->region = $region;
    }

    public function getQuantityDeaths(): int
    {
        return $this->quantityDeaths;
    }

    public function setQuantityDeaths(int $quantityDeaths): void
    {
        $this->quantityDeaths = $quantityDeaths;
    }


    public function getQuantityConfirmed(): int
    {
        return $this->quantityConfirmed;
    }
    public function setQuantityConfirmed(int $quantityConfirmed): void
    {
        $this->quantityConfirmed = $quantityConfirmed;
    }
}