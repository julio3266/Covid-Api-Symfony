<?php
declare(strict_types=1);

namespace App\DataFixtures;


use App\Entity\Region;
use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $regionRepository = $manager->getRepository(Region::class);

        /** @var Region $nordeste */
        $nordeste = $regionRepository->findOneBy(["name"=>"Nordeste"]);

        /** @var Region $norte */
        $norte = $regionRepository->findOneBy(["name"=>"Norte"]);

        $states = [
            new State("Ceará", "ce", $nordeste),
            new State("Maranhão", "ma", $nordeste),
            new State("Bahia", "ba", $nordeste),
            new State("Alagoas", "al", $nordeste),
            new State("Paraíba", "pb", $nordeste),
            new State("Pernambuco", "pe", $nordeste),
            new State("Piauí", "pi", $nordeste),
            new State("Rio Grande do Norte", "rn", $nordeste),
            new State("Sergipe","se", $nordeste),

            new State("Amazonas", "am", $norte),
        ];
        foreach ($states as $state)
        {
            $manager->persist($state);
            $manager->flush();
        }

    }

    public function getDependencies()
    {
        return [
          RegionFixtures::class
        ];
    }
}