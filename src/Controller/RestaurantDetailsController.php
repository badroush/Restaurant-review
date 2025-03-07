<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestaurantDetailsController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire', methods: ['POST'])]
    public function ajouterCommentaire(Request $request, EntityManagerInterface $entityManager): Response
    {
        dump($request);
        $contenu = $request->request->get('comment');
        if ($contenu) {
            $commentaire = new Commentaire();
            $commentaire->setContenu($contenu);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succès !');
        } else {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
        }

        return $this->redirectToRoute('app_commentaire');
    }

}
