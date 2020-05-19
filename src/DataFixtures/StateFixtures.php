<?php
declare(strict_types=1);

namespace App\DataFixtures;


use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $states = [

            new State("Ceará", "ce", "nordeste"),
            new State("Maranhão", "ma", "nordeste"),
            new State("Bahia", "ba", "nordeste"),

        ];
        foreach ($states as $state)
        {
            $manager->persist($state);
            $manager->flush();
        }

    }
}