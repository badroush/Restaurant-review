<?php

namespace App\Controller;
// src/Controller/ResetPasswordController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordHasherInterface; // Utiliser PasswordHasherInterface
use App\Form\ResetPasswordType; // Le formulaire pour le reset
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class ResetPasswordController extends AbstractController
{
    #[Route('/reset/password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $user = $security->getUser();

        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour changer votre mot de passe.');
        }

        $currentPassword = $request->request->get('current_password');
        $newPassword = $request->request->get('new_password');

        // Vérifier l'ancien mot de passe
        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            return $this->redirectToRoute('app_profile'); // ou la route de ton profil
        }

        // Hash et enregistre le nouveau mot de passe
        $user->setPassword(
            $passwordHasher->hashPassword($user, $newPassword)
        );
        $entityManager->flush();

        $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

        return $this->redirectToRoute('app_profile'); // ou autre route selon ton app
    }
}