<?php

namespace App\Controller;

use App\Entity\Visitor;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\VisitorRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $adRepo, UserRepository $userRepo, ObjectManager $manager, VisitorRepository $visitorRepo)
    {
        $visitor = new Visitor();

        $ip = md5($_SERVER["REMOTE_ADDR"]);

        $result = $visitorRepo->findoneby(['hashIp' => $ip]);
        if (!$result) {
            $visitor->setHashIp($ip);
            $manager->persist($visitor);
            $manager->flush();
        }

        return $this->render(
            'homepage.html.twig', [
                'ads' => $adRepo->findBestAds(),
                'users' => $userRepo->findBestUsers(),
            ]
        );

    }
}
