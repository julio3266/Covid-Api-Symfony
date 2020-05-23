<?php
declare(strict_types=1);

namespace App\DataFixtures;


use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RegionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $regions = [
            new Region("Nordeste"),
            new Region("Norte"),
            new Region("Centro-Oeste"),
            new Region("Sudeste"),
            new Region("Sul"),
        ];
        foreach ($regions as $region)
        {
            $manager->persist($region);
            $manager->flush();
        }
    }



}