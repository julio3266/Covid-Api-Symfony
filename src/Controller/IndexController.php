<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use Cassandra\Date;
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
        $yesterday = (new \DateTime())->modify("-1 day");
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select("region")
            ->from(Region::class, "region")
            ->where("region.date between :de and :ate")
            ->setParameter("de", $yesterday->format("Y-m-d 00:00:00"))
            ->setParameter("ate", $yesterday->format("Y-m-d 23:59:59"));
        $regions = $qb->getQuery()->getResult();


        $allRegions = $this->regionRepository->findAll();

        $regionsTimeLine = [];
        $regionsTimeLineConfirmed = [];
        /** @var  Region $region */
        foreach($allRegions as $region) {
            $name = $region->getName();
            $regionsTimeLine[$name] = $regionsTimeLine[$name] ?? [];
            $regionsTimeLineConfirmed[$name] = $regionsTimeLineConfirmed[$name] ?? [];

             array_push($regionsTimeLine[$name], $region->getQuantityDeaths());
             array_push($regionsTimeLineConfirmed[$name], $region->getQuantityConfirmed());

        }


        $allRegionsName = array_map(fn(Region $region) =>   $region->getName(), $allRegions);
        $allDays = array_map(fn(Region $region) =>   $region->getDate()->format('d-m-Y'), $allRegions);

        return $this->render("index/index.html.twig" , [
            "regions"=>$regions,
            "regionsName"=>$allRegionsName,
            "regionsTimeLine"=>$regionsTimeLine,
            "regionsTimeLineConfirmed"=>$regionsTimeLineConfirmed,
            "allDays"=>array_unique($allDays)
        ]);
    }

    public function stateAction(string $region): Response
    {


        $yesterday = (new \DateTime())->modify("-1 day");
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select("state")
            ->from(State::class, "state")
            ->where("state.region =:region and state.date between :de and :ate")
                ->setParameter("region", $region)
            ->setParameter("de", $yesterday->format("Y-m-d 00:00:00"))
            ->setParameter("ate", $yesterday->format("Y-m-d 23:59:59"));

        $states = $qb->getQuery()->getResult();


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

        $state = $this->stateRepository->find($uf);

        if (!$state) {
            $this->addFlash("error", "Estado não encontrado");
            return $this->redirectToRoute("index-state");
        }

        $cities = $this->cityRepository->findBy([
            "state"=>$state
        ]);


        return $this->render("index/city.html.twig", [
            "cities"=>$cities
        ]);
    }
}