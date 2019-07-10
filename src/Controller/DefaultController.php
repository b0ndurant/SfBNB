<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController {
    
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $adRepo, UserRepository $userRepo){
        return $this->render(
            'homepage.html.twig', [
                'ads' => $adRepo->findBestAds(),
                'users' => $userRepo->findBestUsers()
            ]
        );

    }
}

?>