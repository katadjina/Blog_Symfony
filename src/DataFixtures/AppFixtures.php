<?php

namespace App\DataFixtures;


use DateTime;
use App\Entity\User;
use App\Entity\MicroPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ){
    }

    public function load(ObjectManager $manager): void
    {


        $user1 = new User();
        $user1->setEmail('test@example.com');
        $user1->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user1,
                '12345678'
                )
            );
            $manager->persist($user1);
        
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user2,
                '12345678'
                )
            );
            $manager->persist($user2);  



        $microPost1 = new MicroPost();
        $microPost1->setTitle('First Post');
        $microPost1->setText('Hi User! This is the very first post');
        $microPost1->setCreated(new DateTime());
        $microPost1->setAuthor($user2);
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Second Post');
        $microPost2->setText('Posting posts is great, I love it');
        $microPost2->setCreated(new DateTime());
        $microPost2->setAuthor($user2);
        $manager->persist($microPost2);

        $manager->flush();

     



    }
}
