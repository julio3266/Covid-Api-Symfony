<?php
declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StateController extends AbstractController
{
    public function getAction()
    {
        return $this->json([
           "Teste-GET",
        ]);
    }
}