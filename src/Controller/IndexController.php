<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\City;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    private EntityManager $entityManager;
    private ObjectRepository $cityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cityRepository = $entityManager->getRepository(City::class);
    }
    public function indexAction(): Response
    {
        return $this->render("index/index.html.twig");
    }
    public function cityAction(): Response
    {
        $cities = $this->cityRepository->findAll();

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