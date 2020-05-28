<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity()
 */
class Region
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
     * @Column(type="integer")
     */
    public int $quantityDeaths;

    /**
     * @Column(type="integer")
     */
    public int $quantityConfirmed;


    public function __construct(string $name, int $quantityConfirmed, int $quantityDeaths)
    {
        $this->name = $name;
        $this->quantityConfirmed = $quantityConfirmed;
        $this->quantityDeaths = $quantityDeaths;
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


    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }




}