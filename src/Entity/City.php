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
class City
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
     * @ManyToOne(targetEntity="State")
     * @JoinColumn(name="state_id", referencedColumnName="id")
     */
    public State $state;

    /**
     * @Column(type="datetime")
     */
    public \DateTime $date;


    /**
     * @Column(type="integer")
     */
    public int $quantityConfirmed;


    /**
     * @Column(type="integer")
     */
    public int $quantityDeaths;


    public function __construct(string $name, State $state)
    {
        $this-> name = $name;
        $this-> state = $state;
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


    public function getState(): State
    {
        return $this->state;
    }
    public function setState(State $state): void
    {
        $this->state = $state;
    }


    public function getDate(): \DateTime
    {
        return $this->date;
    }
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getQuantityConfirmed(): int
    {
        return $this->quantityConfirmed;
    }
    public function setQuantityConfirmed(int $quantityConfirmed): void
    {
        $this->quantityConfirmed = $quantityConfirmed;
    }

    public function getQuantityDeaths(): int
    {
        return $this->quantityDeaths;
    }
    public function setQuantityDeaths(int $quantityDeaths): void
    {
        $this->quantityDeaths = $quantityDeaths;
    }

}