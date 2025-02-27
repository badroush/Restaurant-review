<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RestaurantsRepository;
use App\Entity\Restaurants;
use App\Entity\Evaluation;
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(restaurantsRepository $restaurantsRepository): Response
    {
       // Récupérer les restaurants triés par la meilleure note
       $restaurants = $restaurantsRepository->findBestRated();
      
       return $this->render('home\index.html.twig', [
           'restaurants' => $restaurants,
       ]);
    }
    #[Route('/restaurant/{id}', name: 'restaurant_details', requirements: ['id' => '\d+'])]
    public function details(Restaurants $restaurant): Response
    {
        return $this->render('restaurant_details/index.html.twig', [
            'restaurant' => $restaurant,
            'evaluations' => $restaurant->getEvaluations(), // Passer les avis à la vue
        ]);
    }
}
