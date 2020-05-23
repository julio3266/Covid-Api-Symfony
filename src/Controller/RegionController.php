<?php
declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegionController extends AbstractController
{
    public function getAction()
    {
        return $this->json([
            "GET-test"
        ]);
    }
}