<?php

declare(strict_types=1);

namespace App\Command;


use App\Entity\City;
use App\Entity\State;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class CovidCommand extends Command
{
    private EntityManager $doctrine;
    protected static $defaultName="app:covid:update";

    public function __construct(EntityManager $doctrine)
    {
        date_default_timezone_set("America/Fortaleza");
        $this -> doctrine = $doctrine;
        parent::__construct(self::$defaultName);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output -> writeln("====================================================");
        $output -> writeln("=== Updating covid...");
        $output -> writeln("====================================================");
        $this -> updateCE();
        return 0;
    }

    public function configure()
    {
        $this -> setDescription("This command update data covid");
    }

    public function updateCE():void
    {
        $today = date("Y-m-d");
        $type = "Confirmado,Óbito,Recuperado";
        $httpClient = HttpClient::create();
        $response = $httpClient -> request("GET", "https://indicadores.integrasus.saude.ce.gov.br/api/coronavirus/qtd-por-municipio?data={$today}&tipo={$type}&idMunicipio=&idRegiaoSaude=&idMacrorregiao=");

        $ce = $this -> doctrine -> getRepository(State::class) -> findOneBy(["uf" => "ce"]);
        foreach($response -> toArray() as $objeto) {
            $city = new City();
            $city -> setState($ce);
            $city -> setName($objeto  ["municipio"]);
            $city -> setQuantity($objeto  ["quantidade"]??0);
            $city -> setDate(new \DateTime());
            $city -> setType($objeto ["tipo"]);
            $this -> doctrine -> persist($city);
            $this -> doctrine -> flush();
        }
    }
}