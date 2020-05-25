<?php

declare(strict_types=1);

namespace App\Command;


use App\Entity\City;
use App\Entity\State;
use Doctrine\ORM\EntityManager;
use http\Env\Response;
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
        $this -> updateMA();
        $this-> updateBA();;
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

    public function updateMA():void
    {
        $type =  "cases_c, deaths ";
        $httpClient = HttpClient::create();
        $response = $httpClient-> request("GET", "https://mapa-covid19.saude.ma.gov.br/data.php?type=ma&_=1590069094803");

        $ma = $this->doctrine->getRepository(State::class) -> findOneBy(["uf"=>"ma"]);
        foreach ($response->toArray() as $objeto) {
            if(!isset($objeto["name"])) {
                continue;
            }
            $city = new City();
            $city -> setState($ma);
            $city -> setName($objeto ["name"]);
            $city -> setType("Óbito");
            $city -> setQuantity((int) $objeto  ["deaths"]);
            $city -> setDate(new \DateTime());
            $this-> doctrine -> persist($city);

            $city = new City();
            $city -> setState($ma);
            $city -> setName($objeto ["name"]);
            $city -> setType("Confirmado");
            $city -> setQuantity((int) $objeto  ["cases_c"]);
            $city -> setDate(new \DateTime());
            $this-> doctrine -> persist($city);

            $this -> doctrine -> flush();
        }
    }
    public function updateBA():void
    {

        $httpClient = HttpClient::create();
        $response = $httpClient->request(
            "POST",
            "https://infovis.sei.ba.gov.br/covid19/session/80706ff8ad431f3e4ff655a4fc0aa4f0/dataobj/TabPorCidade?w=&nonce=8053573957c43f1d",
            [
                "body"=>["text"=>"draw=1&columns%5B0%5D%5Bdata%5D=0&columns%5B0%5D%5Bname%5D=&columns%5B0%5D%5Bsearchable%5D=true&columns%5B0%5D%5Borderable%5D=true&columns%5B0%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B0%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B1%5D%5Bdata%5D=1&columns%5B1%5D%5Bname%5D=&columns%5B1%5D%5Bsearchable%5D=true&columns%5B1%5D%5Borderable%5D=true&columns%5B1%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B1%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B2%5D%5Bdata%5D=2&columns%5B2%5D%5Bname%5D=&columns%5B2%5D%5Bsearchable%5D=true&columns%5B2%5D%5Borderable%5D=true&columns%5B2%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B2%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B3%5D%5Bdata%5D=3&columns%5B3%5D%5Bname%5D=&columns%5B3%5D%5Bsearchable%5D=true&columns%5B3%5D%5Borderable%5D=true&columns%5B3%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B3%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B4%5D%5Bdata%5D=4&columns%5B4%5D%5Bname%5D=&columns%5B4%5D%5Bsearchable%5D=true&columns%5B4%5D%5Borderable%5D=true&columns%5B4%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B4%5D%5Bsearch%5D%5Bregex%5D=false&start=0&length=-1&search%5Bvalue%5D=&search%5Bregex%5D=false&search%5BcaseInsensitive%5D=true&search%5Bsmart%5D=true&escape=false"]
            ]
        );
        $ba = $this->doctrine->getRepository(State::class)->findOneBy(["uf" => "ba"]);
        foreach ($response->toArray()["data"] as $objeto) {
            if (!isset($objeto[0])) {
                continue;
            }


            $city = new City();
            $city->setState($ba);
            $city->setName($objeto [0]);
            $city->setType("Óbito");
            $city->setQuantity((int)$objeto  [3]);
            $city->setDate(new \DateTime());
            $this->doctrine->persist($city);

            $city = new City();
            $city->setState($ba);
            $city->setName($objeto [0]);
            $city->setType("Confirmado");
            $city->setQuantity((int)$objeto  [1]);
            $city->setDate(new \DateTime());
            $this->doctrine->persist($city);

            $this->doctrine->flush();
        }
    }
}

