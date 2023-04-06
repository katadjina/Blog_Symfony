<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MicroPostController extends AbstractController
{
    #[Route('/all')]
    #[Route('/post')]
    #[Route('/blog')]
    #[Route('/home')]
    #[Route('/micro-post')]
    #[Route('/', name: 'app_micro_post')]
    public function index(MicroPostRepository $rep): Response
    {
        //dd($posts->findAll());
        //repo method
        return $this->render('micro_post/index.html.twig', [
            'posts' => $rep->findAll(),
        ]);
    }

    //showOny by ID
    //Param Converter
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        //dd($posts->find(id));

        return $this->render('micro_post/show.html.twig', [

            'post' => $post,
        ]);
    }

    // OR-->
  
    // public function showOne($id, MicroPostRepository $posts): Response
    // {
    //     //dd($posts->find(id));

    // }




    //denying access to website visitors that are not authenticated
    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2, )]  
    #[IsGranted('IS_AUTHENTICATED_FULLY')] 
    public function add( Request $request, MicroPostRepository $posts ): Response{
        //testing
        // $form = $this->createForm(MicroPostType::class, new MicroPost());  //available bc of the Abstract controller
        
        $microPost = new MicroPost();
        $form = $this->createFormBuilder($microPost)
            ->add('title')
            ->add('text')
            ->add('category')
            ->getForm();

      
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $post->setCreated(new DateTime());
            $post->setAuthor($this->getUser());
            $posts->add($post, true);
            // dd($post);

            //Add a flash message
            $this->addFlash('success', 'Post added successfully');

            return $this->redirectToRoute('app_micro_post');
            //Redirect
        }

        return $this->render( 
            'micro_post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }

    
    // #[Route('/micro-post/delete/{id}', name: 'app_micro_post_delete' )]  
    // #[IsGranted('IS_AUTHENTICATED_FULLY')] 
    // public function delete(ManagerRegistry $doctrine, int $id): Response
    // {

    //     $entityManager = $doctrine->getManager();
    //     $post = $entityManager->getRepository(MicroPost::class)->find($id);

    //     $entityManager->remove($post);
    //     $entityManager->flush();
        

    //      //Add a flash message
    //      $this->addFlash('error', 'Post remove');
      
    //     return $this->redirectToRoute('app_micro_post', [], Response::HTTP_SEE_OTHER);
    // }



    //DELETE 2


    #[Route('/micro-post/delete/{id}', name: 'app_micro_post_delete' )]  
    #[IsGranted('IS_AUTHENTICATED_FULLY')] 
    public function delete(MicroPost $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();
    
        return new Response(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit' )]   
    //has to be logged in to edit the post
    // #[IsGranted('ROLE_EDITOR')]  
    // #[IsGranted(MicroPost::EDIT, 'post')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] 
    public function edit(MicroPost $post, Request $request, MicroPostRepository $posts): Response
    {
        
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('text')
            ->add('category')
            ->getForm();
        
        $form->handleRequest($request);

        $this->denyAccessUnlessGranted(MicroPost::EDIT, $post);


        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            //$post->setCreated(new DateTime());  we are not creating new date -> we keep the original date
            $post->setAuthor($this->getUser());
            $posts->add($post, true);
            // dd($post);

            //Add a flash message
            $this->addFlash('success', 'Post has been updated');

            return $this->redirectToRoute('app_micro_post');
            //Redirect
        }

        return $this->render(  
            'micro_post/edit.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }




    //comment


    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment' )]
    //user has to be logged in to comment
    //#[IsGranted('ROLE_COMMENTER')]
    //just needs to be authenticated to comment
    #[IsGranted('IS_AUTHENTICATED_FULLY')]   
    public function addComment(MicroPost $post, Request $request, CommentRepository $comments): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();
            $comment->setPost($post);
            //getting the current user
            $comment->setAuthor($this->getUser());
            $comments->save($comment, true); //from comment repo

            //Add a flash message
            $this->addFlash('success', 'Comment added successfully');

            return $this->redirectToRoute(
                'app_micro_post_show', 
                ['post' => $post->getId()]
            );
            //Redirect
        }

        return $this->render(  
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }

}
