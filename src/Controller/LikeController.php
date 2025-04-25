<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Like;
use App\Entity\Restaurants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request; // <-- CET IMPORT MANQUAIT
use Symfony\Bundle\SecurityBundle\Security; // Nouveau namespace recommandé
use Symfony\Component\Security\Core\User\UserInterface;
final class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like', methods: ['POST'])]
public function like(
    Request $request,
    EntityManagerInterface $entityManager,
    Security $security,
    int $id
): JsonResponse {
    // Récupérer le restaurant
    $restaurant = $entityManager->getRepository(Restaurants::class)->find($id);
    if (!$restaurant) {
        return new JsonResponse(['success' => false, 'error' => 'Restaurant not found'], 404);
    }
    // Vérifier l'utilisateure
    $user = $security->getUser();
    if (!$user) {
        return new JsonResponse(['success' => false, 'error' => 'Authentication required'], 401);
    }
    // Gérer le like/dislike
    $likeRepository = $entityManager->getRepository(Like::class);
    $existingLike = $likeRepository->findOneBy([
        'user' => $user,
        'restaurant' => $restaurant
    ]);
    if ($existingLike) {
        $entityManager->remove($existingLike);
        $isLiked = false;
    } else {
        $like = new Like();
        $like->setUser($user)
             ->setRestaurant($restaurant);
        $entityManager->persist($like);
        $isLiked = true;
    }
    $entityManager->flush();
    // Calculer le nouveau nombre de likes
    $likesCount = $likeRepository->count(['restaurant' => $restaurant]);
    return new JsonResponse([
        'success' => true,
        'isLiked' => $isLiked,
        'likesCount' => $likesCount,
    ]);
}
        
}