<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RestaurantsRepository;
use App\Entity\Restaurants;
use App\Entity\Evaluation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;



final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(restaurantsRepository $restaurantsRepository,Security $security): Response
    {
        $userId = $security->getUser()?->getId();
        $restaurants = $restaurantsRepository->findBestRated(8, $userId);
    
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants
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