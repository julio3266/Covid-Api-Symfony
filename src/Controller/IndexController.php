<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\City;
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

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->cityRepository = $entityManager->getRepository(City::class);
        $this->stateRepository = $entityManager->getRepository(State::class);
    }

    public function indexAction(): Response
    {
        $states = $this->stateRepository->findAll();


        return $this->render("index/index.html.twig" , [
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