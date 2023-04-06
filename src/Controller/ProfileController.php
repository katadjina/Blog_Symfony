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
            //image handling --->
            //obtenir le ficher a la main
             //save -> flash -> redirect
            $fichier = $form['image']->getData();
            $dossier = $this->getParameter('kernel.project_dir').'/public/dossierFichiers';
            if ($fichier) {
                // obtenir un nom de fichier unique pour éviter les doublons dans le dossier
                $nomFichierServeur = md5(uniqid()) . "." . $fichier->guessExtension();
                // stocker le fichier dans le serveur (on peut indiquer un dossier)
                $fichier->move($dossier, $nomFichierServeur);
                // affecter le nom du fichier de l'entité. Ça sera le nom qu'on
                // aura dans la BD (un string, pas un objet UploadedFile cette fois)
                $userProfile->setImage($nomFichierServeur); 
            }
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


//perhaps only visible when user is lgged in
    #[Route('/profile/{id}', name: 'app_profile_show')]
    public function show(User $user): Response
    {
        return $this->render('profile/show.html.twig' , [
            'user' => $user
        ]);
    }

    // #[Route('/profile/profile-image', name: 'app_profile_image')]
    // #[IsGranted('IS_AUTHENTICATED_FULLY')]
    // public function profileImage(): Response {
    //     return $this->render(
    //         'profile/profile_image.html.twig',
    //         [

    //         ]
    //         );
    // }
}
