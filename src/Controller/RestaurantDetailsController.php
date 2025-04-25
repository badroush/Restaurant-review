<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Evaluation;
use App\Repository\RestaurantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Repository\EvaluationRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;


final class RestaurantDetailsController extends AbstractController
{
    
    #[Route('/commentaire', name: 'app_commentaire', methods: ['POST'])]
    public function ajouterCommentaire(Request $request, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, RestaurantsRepository $restaurantRepo): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $tokenStorage->getToken()?->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Récupérer les données du formulaire
        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');
        $restaurantId = $request->request->get('restaurant_id');
        // Trouver le restaurant en base
        $restaurant = $restaurantRepo->find($restaurantId);
        if (!$restaurant) {
            throw $this->createNotFoundException("Restaurant introuvable.");
        }
        // Créer une nouvelle évaluation
        $evaluation = new Evaluation();
        $evaluation->setUser($user);
        $evaluation->setRestaurant($restaurant);
        $evaluation->setRating((int) $rating);
        $evaluation->setCommentaire($comment);
        $evaluation->setDatePublication(new \DateTimeImmutable());
        if (empty($rating)) {
            $this->addFlash('error', 'Veuillez sélectionner une note avant de soumettre votre commentaire.');
        } elseif (empty($comment)) {
            $this->addFlash('error', 'Veuillez entrer un commentaire avant de soumettre votre évaluation.');
        } else {
            
            $entityManager->persist($evaluation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire et évaluation ont été enregistrés avec succès !');
        }
        
    return $this->redirectToRoute('restaurant_details', [
            'id' => $restaurantId,

        ]);
    }
    #[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['DELETE'])]
public function deleteCommentaire(
    $id, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    evaluationRepository $commentaireRepo, 
    CsrfTokenManagerInterface $csrfTokenManager
): JsonResponse 
{
    // Lire et décoder le JSON du body
    $data = json_decode($request->getContent(), true);

    // Vérifier que $data est bien défini et que le token existe
    if (!isset($data['_token'])) {
        return $this->json([
            'status' => 'error',
            'message' => 'Token CSRF manquant.'
        ], 400);
    }

    // Trouver le commentaire
    $commentaire = $commentaireRepo->find($id);
    if (!$commentaire) {
        return $this->json([
            'status' => 'error',
            'message' => 'Commentaire introuvable.'
        ], 404);
    }

    // Vérifier le token CSRF
    if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete-comment', $data['_token']))) {
        return $this->json([
            'status' => 'error',
            'message' => 'Token CSRF invalide.'
        ], 403);
    }

    // Vérifier que l'utilisateur est le bon
    if ($commentaire->getUser() !== $this->getUser()) {
        return $this->json([
            'status' => 'error',
            'message' => 'Vous ne pouvez pas supprimer ce commentaire.'
        ], 403);
    }

    // Suppression
    $entityManager->remove($commentaire);
    $entityManager->flush();

    return $this->json([
        'status' => 'success',
        'message' => 'Commentaire supprimé avec succès.'
    ]);
}



#[Route('/comment/update/{id}', name: 'app_edit_comment')]
public function editCommentaire(
    int $id, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    EvaluationRepository $commentaireRepo
): Response {
    $commentaire = $commentaireRepo->find($id);
    if (!$commentaire) {
        throw $this->createNotFoundException('Commentaire introuvable.');
    }
    // Vérifie si l'utilisateur est l'auteur du commentaire
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous ne pouvez pas modifier ce commentaire.");
    }
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Commentaire modifié avec succès !');

        return $this->redirectToRoute('app_home');
    }
    return $this->render('commentaire/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}


}