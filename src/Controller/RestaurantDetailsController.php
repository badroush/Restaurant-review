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

        // Sauvegarder en base
        $entityManager->persist($evaluation);
        $entityManager->flush();

        // Rediriger avec un message de succès
        $this->addFlash('success', 'Votre avis a été ajouté avec succès !');
        return $this->redirectToRoute('restaurant_details', ['id' => $restaurantId]);
    }

    #[Route('/comment/delete/{id}', name: 'app_delete_comment', methods: ['POST'])]
public function deleteCommentaire(
    int $id, 
    EntityManagerInterface $entityManager, 
    EvaluationRepository $commentaireRepo
): Response {
    $commentaire = $commentaireRepo->find($id);
    
    if (!$commentaire) {
        throw $this->createNotFoundException('Commentaire introuvable.');
    }

    // Vérifie si l'utilisateur est l'auteur du commentaire
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer ce commentaire.");
    }

    $entityManager->remove($commentaire);
    $entityManager->flush();

    $this->addFlash('success', 'Commentaire supprimé avec succès !');

    return $this->redirectToRoute('restaurant_details', ['id' => $commentaire->getRestaurant()->getId()]);
    // Redirige vers la page de détails du restaurant
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