<?php

namespace App\Controller;

use App\Entity\UserProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// getUserProfile -->
use App\Repository\UserRepository;   
use App\Entity\User;


class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request): Response
    {

        // ???? ---> 
       /**  @var User $user */ 
        $user = $this->getUser();
        // if null create new
        $userProfile = $user->getUserProfile() ?? new UserProfile();


        $form = $this->createForm(
            UserProfileType::class, $userProfile
        );

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $userProfile = $form->getData();
            //save -> flash -> redirect
        }
        return $this->render(
            'profile/profile.html.twig', [
            'forl' => $form->createView(),
        ]);
    }
}
