<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts): Response
    {
        //dd($posts->findAll());
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }


    //function for showing only one post by its id
    //using Param Converter
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        //dd($post);

        return $this->render('micro_post/show.html.twig', [

            'post' => $post,
        ]);
    }


    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2, )]  
    #[IsGranted('IS_AUTHENTICATED_FULLY')] 
    public function add(
        Request $request, 
        MicroPostRepository $posts
    ): Response{
        //denying access to website visitors that are not authenticated
        //one of the way of limiting user's access to controllers method
        //so if in url you will type /micro-post/add you will be redirected to login page if u r not logged in
        //this can be done also by using attributes (below the route)

        //the same is done by using the attribute --> there are slight dfferences 
        // $this->denyAccessUnlessGranted(
        //     'IS_AUTHENTICATED_FULLY'
        // );


        $form = $this->createForm(MicroPostType::class, new MicroPost());  //available bc of the Abstract controller
           


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
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

        return $this->render(  //not renderForm ??
            'micro_post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }


    //update method


    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit' )]   //methods: ['GET']??
    //has to be logged in to edit the post
    // #[IsGranted('ROLE_EDITOR')]  
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $posts): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
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

        return $this->render(  //not renderForm ??
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
