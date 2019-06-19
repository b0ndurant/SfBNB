<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdController extends Controller
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * show ad
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(adRepository $repo, $slug) 
    {
        $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            "ad" => $ad,
        ]);
    }
}
