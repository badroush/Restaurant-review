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
use Symfony\Component\HttpFoundation\Request;




final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(restaurantsRepository $restaurantsRepository,Security $security,Request $request): Response
    {

        $userId = $security->getUser()?->getId();
        $restaurants = $restaurantsRepository->findBestRated(8, $userId);
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants,
            'showAll' => true // Nouveau flag pour le template
        ]);
        
    }

    #[Route('/filtre', name: 'app_filre')]
    public function filter(restaurantsRepository $restaurantsRepository,Security $security,Request $request): Response
    {

        $userId = $security->getUser()?->getId();
        $filter = $request->query->get('f');
       
        switch ($filter) {
            case 'most-commented':
                $restaurants = $restaurantsRepository->findMostCommented($userId);
               
                break;
            case 'best-rated':
                $restaurants = $restaurantsRepository->findBestRated(8, $userId);
               
                break;
            case 'all':
            default:
                $restaurants = $restaurantsRepository->findall($userId);
               
                break;
        }
        // $restaurants = $restaurantsRepository->findBestRated(8, $userId);
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants,
            'filter' => $filter,
            'showAll' => true // Nouveau flag pour le template
        ]);
        
    }
    #[Route('/alert/{type}', name: 'app_alert')]
public function showAlert(string $type): JsonResponse
{
    $alerts = [
        'no_result' => [
            'position' => 'center',
            'title' => 'Aucun résultat trouvé',
            'text' => 'pas de resultat pour ce recheche',
            'icon' => 'info',
            'showConfirmButton'=>  'false',
            'timer' => 1500,
            'redirectRoute' => $this->generateUrl('app_home')
            //'redirectRoute' => $this->generateUrl('app_save_success')
        ],
        'success' => [
            'position' => 'center',
            'title' => 'Succès',
            'text' => 'Opération réalisée avec succès.',
            'icon' => 'success',
            'confirmButtonText' => 'Super',
            'timer' => 1500,
             'redirectRoute' => ''
            //'redirectRoute' => $this->generateUrl('app_home')
        ],
        'error' => [
            'position' => 'center',
            'title' => 'Aucun résultat trouvé',
            'text' => 'erreur de chargement',
            'icon' => 'error',
            'showConfirmButton'=>  'false',
            'timer' => 1500,
             'redirectRoute' => ''
        ],
        'empty_field' => [
            'position' => 'center',
            'title' => 'Champ vide',
            'text' => 'veillez remplir le champ de recherche',
            'icon' => 'warning',
            'showConfirmButton'=>  'false',
            'timer' => 1500,
             'redirectRoute' => $this->generateUrl('app_home')
        ],
        'not_connected' => [
            'position' => 'center',
            'title' => 'Non connecte',
            'text' => 'veillez vous connecter avant de faire une action',
            'icon' => 'warning',
            'showConfirmButton'=>  'false',
            'timer' => 1500,
             'redirectRoute' => ''
        ],
    ];

    if (!isset($alerts[$type])) {
        return new JsonResponse(['error' => 'Type d\'alerte inconnu'], 400);
    }

    return new JsonResponse($alerts[$type]);
}

    #[Route('/restaurant/{id}', name: 'restaurant_details', requirements: ['id' => '\d+'])]
    public function details(Restaurants $restaurant,Request $request): Response
    {
        return $this->render('restaurant_details/index.html.twig', [
            'restaurant' => $restaurant,
            'evaluations' => $restaurant->getEvaluations(), // Passer les avis à la vue
        ]);
    }
   
}