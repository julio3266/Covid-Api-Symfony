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

        /** @var Region $centroOeste */
        $centroOeste = $regionRepository->findOneBy(["name"=>"Centro-Oeste"]);

        /** @var Region $sudeste */
        $sudeste = $regionRepository->findOneBy(["name"=>"Sudeste"]);

        /** @var Region $sul */
        $sul = $regionRepository->findOneBy(["name"=>"Sul"]);

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
            new State("Amapá", "ap", $norte),
            new State("Acre", "ac", $norte),
            new State("Pará", "pa", $norte),
            new State("Rondônia", "ro", $norte),
            new State("Roraima", "rr", $norte),
            new State("Tocantins", "to", $norte),

            new State("Distrito Federa", "df", $centroOeste),
            new State("Goiás", "go", $centroOeste),
            new State("Mato Grosso", "mt", $centroOeste),
            new State("Mato Grosso do Sul", "ms", $centroOeste),

            new State("Espírito Santo", "es", $sudeste),
            new State("Minas Gerais", "mg", $sudeste),
            new State("Rio de Janeiro", "rj", $sudeste),
            new State("São Paulo", "sp", $sudeste),

            new State("Paraná", "pr", $sul),
            new State("Rio Grande do Sul", "rs", $sul),
            new State("Santa Catarina", "sc", $sul),
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