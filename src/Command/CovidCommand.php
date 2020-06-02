<?php

declare(strict_types=1);

namespace App\Command;


use App\Entity\City;
use App\Entity\Region;
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
        $this->updateRegion();
        $this->updateState();
        $this->updateCity();
        return 0;
    }

    public function configure()
    {
        $this -> setDescription("This command update data covid");
    }

    public function updateRegion():void
    {
        $url = "https://xx9p7hp1p7.execute-api.us-east-1.amazonaws.com/prod/PortalSintese";
        $httpClient = HttpClient::create();
        $response = $httpClient -> request("GET", $url);

        foreach ($response -> toArray() as $objeto) {
            $region = new Region(
                $objeto ["regiao"] ?? $objeto ["_id"],
                (int) $objeto ["casosAcumulado"],
                (int) $objeto ["obitosAcumulado"]
            );

            $this->doctrine->persist($region);
            $this->doctrine->flush();
        }
    }

    public function updateState()
    {
        $states = [

            "AC"=> ["nome"=>"Acre", "regiao"=> "Norte"],
            "AL"=> ["nome"=>"Alagoas", "regiao"=> "Nordeste"],
            "AP"=> ["nome"=>"Amapá", "regiao"=> "Norte"],
            "AM"=> ["nome"=>"Amazonas", "regiao"=> "Norte"],
            "BA"=> ["nome"=>"Bahia", "regiao"=> "Nordeste"],
            "CE"=> ["nome"=>"Ceará", "regiao"=> "Nordeste"],
            "DF"=> ["nome"=>"Distrito Federal", "regiao"=> "Centro-Oeste"],
            "ES"=> ["nome"=>"Espírito Santo", "regiao"=> "Sudeste"],
            "GO"=> ["nome"=>"Goiás", "regiao"=> "Centro-Oeste"],
            "MA"=> ["nome"=>"Maranhão", "regiao"=> "Nordeste"],
            "MT"=> ["nome"=>"Mato Grosso", "regiao"=> "Centro-Oeste"],
            "MS"=> ["nome"=>"Mato Grosso do Sul", "regiao"=> "Centro-Oeste"],
            "MG"=> ["nome"=>"Minas Gerais", "regiao"=> "Sudeste"],
            "PA"=> ["nome"=>"Pará", "regiao"=> "Norte"],
            "PB"=> ["nome"=>"Paraíba", "regiao"=> "Nordeste"],
            "PR"=> ["nome"=>"Paraná", "regiao"=> "Sul"],
            "PE"=> ["nome"=>"Pernambuco", "regiao"=> "Nordeste"],
            "PI"=> ["nome"=>"Piauí", "regiao"=> "Nordeste"],
            "RJ"=> ["nome"=>"Rio de Janeiro", "regiao"=> "Sudeste"],
            "RN"=> ["nome"=>"Rio Grande do Norte", "regiao"=> "Nordeste"],
            "RS"=> ["nome"=>"Rio Grande do Sul", "regiao"=> "Sul"],
            "RO"=> ["nome"=>"Rondônia", "regiao"=> "Norte"],
            "RR"=> ["nome"=>"Roraima", "regiao"=> "Norte"],
            "SC"=> ["nome"=>"Santa Catarina", "regiao"=> "Sul"],
            "SP"=> ["nome"=>"São Paulo", "regiao"=> "Sudeste"],
            "SE"=> ["nome"=>"Sergipe", "regiao"=> "Nordeste"],
            "TO"=> ["nome"=>"Tocantins", "regiao"=> "Norte"]
        ];
        $url = "https://xx9p7hp1p7.execute-api.us-east-1.amazonaws.com/prod/PortalEstado";

        $httpClient = HttpClient::create();
        $response = $httpClient->request("GET", $url);

        foreach ($response->toArray() as $objeto){
            $uf = $objeto["nome"];
            $regionName = $states [$uf]["regiao"];
            $stateName = $states[$uf]["nome"];
            $region = $this->doctrine->getRepository(Region::class)->findOneBy(["name"=>$regionName]);

            $state = new State($stateName, $uf, $region );
            $state->setQuantityConfirmed($objeto["casosAcumulado"]);
            $state->setQuantityDeaths($objeto["obitosAcumulado"]);

            $this->doctrine->persist($state);
            $this->doctrine->flush();
        }

    }

    public function updateCity(){
        $urlAllCities = "https://servicodados.ibge.gov.br/api/v1/localidades/municipios";
        $urlCitiesCovid = "https://xx9p7hp1p7.execute-api.us-east-1.amazonaws.com/prod/PortalMunicipio";
        $httpClient = HttpClient::create();
        $allCitiesResponse = $httpClient->request("GET", $urlAllCities);
        $allCities = [];

        foreach ($allCitiesResponse->toArray() as $city) {
            $ibgeCode = substr((string) $city["id"], 0, 6);
            $allCities[$ibgeCode] = [
                "name"=>$city["nome"],
                "state"=>$city["microrregiao"]["mesorregiao"]["UF"]["sigla"],
            ];
        }

        $allCitiesCovidResponse = $httpClient->request("GET", $urlCitiesCovid);
        foreach ($allCitiesCovidResponse ->toArray() as $objeto) {
            $cityIbge = $allCities[$objeto["cod"]];
            $state = $this->doctrine->getRepository(State::class)->findOneBy(["uf"=>$cityIbge["state"]]);

            $city = new City($objeto["nome"], $state);
            $city-> setQuantityConfirmed($objeto["casosAcumulado"]);
            $city-> setQuantityDeaths($objeto["obitosAcumulado"]);
            $city-> setDate(new \DateTime());
            $this->doctrine->persist($city);
            $this->doctrine->flush();


        }

    }

}

