<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    private EntityManager $entityManager;

    private ObjectRepository $cityRepository;
    private ObjectRepository $stateRepository;
    private ObjectRepository $regionRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->cityRepository = $entityManager->getRepository(City::class);
        $this->stateRepository = $entityManager->getRepository(State::class);
        $this->regionRepository = $entityManager->getRepository(Region::class);
    }

    public function indexAction(): Response
    {
        $regions = $this->regionRepository->findAll();


        return $this->render("index/index.html.twig" , [
            "regions"=>$regions
        ]);
    }

    public function stateAction(string $region): Response
    {
        $region = $this->regionRepository->findOneBy([
            "name"=>$region
        ]);

        $states = $this->stateRepository->findBy([
            "region"=>$region
        ]);

        if(!$states){
            $this->addFlash("error", "Dados inexistentes para a região selecionada");
            return $this->redirectToRoute("index-region");
        }

        return $this->render("index/state.html.twig" , [
            "states"=>$states
        ]);
    }

    public function cityAction(string $uf): Response
    {
        $state = $this->stateRepository->findOneBy([
            "uf"=>$uf
        ]);

        if (!$state) {
            $this->addFlash("error", "Estado não encontrado");
            return $this->redirectToRoute("index-state");
        }

        $cities = $this->cityRepository->findBy([
            "state"=>$state
        ]);

        $dados = [];

        foreach ($cities as $city) {
            $dados[$city->getName()] = $dados[$city->getName()] ?? [];
            $dados[$city->getName()] [$city->getType()] = $city->getQuantity();
            $dados[$city->getName()] ["date"] = $city->getDate();
        }

        return $this->render("index/city.html.twig", [
            "cities"=>$dados
        ]);
    }
}