<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
    public function showOne(MicroPost $post): Response
    {
        //dd($post);

        return $this->render('micro_post/show.html.twig', [

            'post' => $post,
        ]);
    }


    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2, )]   //methods: ['GET']??
    public function add(Request $request, MicroPostRepository $posts): Response
    {
      
        $form = $this->createForm(MicroPostType::class, new MicroPost());  //available bc of the Abstract controller
           


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $post = $form->getData();
                $post->setCreated(new DateTime());
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
    public function edit(MicroPost $post, Request $request, MicroPostRepository $posts): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
            


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $post = $form->getData();
                //$post->setCreated(new DateTime());  we are not creating new date -> we keep the original date
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
    public function addComment(MicroPost $post, Request $request, CommentRepository $comments): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();
            $comment->setPost($post);
            $comments->add($comment, true);

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