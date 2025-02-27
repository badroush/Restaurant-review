<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestaurantDetailsController extends AbstractController
{
    #[Route('/restaurant/details', name: 'app_restaurant_details')]
    public function index(): Response
    {
        return $this->render('restaurant_details/index.html.twig', [
            'controller_name' => 'RestaurantDetailsController',
        ]);
    }
}
