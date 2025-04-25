<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request; // <-- CET IMPORT MANQUAIT
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Restaurants;
use Symfony\Bundle\SecurityBundle\Security; // Nouveau namespace recommandé
use App\Repository\RestaurantsRepository;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request,RestaurantsRepository $repository,EntityManagerInterface $entityManager,Security $security): Response
    {
        $query = $request->query->get('q');
    if (empty($query)) {
        return $this->json([]);
    }
    $userId = $security->getUser()?->getId();
    $results = $repository->findByText($query, 8, $userId);
    return $this->json($results);
    }
}