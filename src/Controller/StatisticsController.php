<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LikeRepository;
use App\Repository\EvaluationRepository;
use App\Repository\RestaurantsRepository;

class StatisticsController extends AbstractController
{
    private $likeRepository;
    private $evaluationRepository;
    private $restaurantsRepository;

    public function __construct(
        LikeRepository $likeRepository,
        EvaluationRepository $evaluationRepository,
        RestaurantsRepository $restaurantsRepository
    ) {
        $this->likeRepository = $likeRepository;
        $this->evaluationRepository = $evaluationRepository;
        $this->restaurantsRepository = $restaurantsRepository;
    }

    #[Route('/admin/statistics', name: 'admin_statistics')]
    public function index(): Response
    {
        $restaurants = $this->restaurantsRepository->findAll();

        $likesCount = [];
        $commentsCount = [];
        $restaurantNames = [];


        $restaurants = $this->restaurantsRepository->findAllRestaurants();

        foreach ($restaurants as $restaurant) {
            $restaurantNames[] = $restaurant->getName(); // ou getName() selon ton entité
            $likesCount[] = $this->likeRepository->countByRestaurant($restaurant);
            $commentsCount[] = $this->evaluationRepository->countByRestaurant($restaurant);
        }

        return $this->render('admin/statistics.html.twig', [
            'restaurantNames' => $restaurantNames,
            'likesCount' => $likesCount,
            'commentsCount' => $commentsCount,
        ]);
    }
}
