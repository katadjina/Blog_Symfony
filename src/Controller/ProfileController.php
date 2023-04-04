<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// getUserProfile -->
use App\Repository\UserRepository;   
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, UserRepository $users): Response
    {

        // ???? ---> 
       /**  @var User $user */ 
        $user = $this->getUser();
        // if null create new
        $userProfile = $user->getUserProfile() ?? new UserProfile();


        $form = $this->createForm(
            UserProfileType::class, 
            $userProfile
        );

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $userProfile = $form->getData();
             //save -> flash -> redirect
            $user->setUserProfile($userProfile);
            $users->add($user, true);
            $this->addFlash('success', 'Profile settings updated successfully');

            return $this->redirectToRoute(
                'app_profile');
           
        }
        return $this->render(
            'profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
