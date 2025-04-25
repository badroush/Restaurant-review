<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class ProfileController extends AbstractController
{ #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/profile/edit', name: 'app_reset_password')]
    public function editProfile(
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifie si le champ du mot de passe est rempli
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                // Hash le mot de passe et le met à jour
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/upload-picture', name: 'app_upload_profile_picture', methods: ['POST'])]
public function uploadPicture(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em, Security $security): JsonResponse
{

    if ($request->isMethod('GET')) {
        return new JsonResponse(['error' => 'Cette route accepte uniquement les requêtes POST.'], 405);
    }
    /** @var UploadedFile $file */
    $file = $request->files->get('profilePicture');
dd($file instanceof UploadedFile);
    if ($file) {
        $user = $security->getUser();
        $filename = uniqid().'.'.$file->guessExtension();
        $file->move($this->getParameter('profile_pictures_directory'), $filename);

        $user->setProfilePicture($filename);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    return new JsonResponse(['success' => false], 400);
}

}
