<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        $profile = $user->getProfile();

        if (!$profile) {
            $profile = new Profile();
            $profile->setIdUser($user);

            $form = $this->createForm(ProfileType::class, $profile);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($profile);
                $em->flush();

                return $this->redirectToRoute('app_profile'); // Redirige vers la page de profil après l'enregistrement
            }

            return $this->render('profile/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Si le profil existe déjà, affiche les informations ou une autre page
        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        $profile = $user->getProfile();

        if (!$profile) {
            return $this->redirectToRoute('app_profile_new');
        }

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        $profile = $user->getProfile();

        if ($profile && $this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $em->remove($profile);
            $em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
