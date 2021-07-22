<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/{_locale}")
 */
class UserController extends AbstractController
{
    /**
     *
     * @Route("/register" ,name="register")
     *
     */
    public function register(Request $requete,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher):Response
    {
        $user = new User();
        $formulaire = $this->createForm(UserType::class,$user);
        $formulaire->handleRequest($requete);
        if($formulaire->isSubmitted() && $formulaire->isValid())
        {
            $hashedPassword = $hasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('login');
        }
        return $this->render('user/register.html.twig',[
            "formulaireRegister" => $formulaire->createView()

        ]);
    }

    /**
     *
     * @Route("/login" , name="login")
     */
    public function login(): Response
    {
        return $this->render('user/login.html.twig');


    }

    /**
     *
     *
     * @Route("/logout" ,name="logout")
     *
     */
    public function logout(): Response
    {
        return $this->render('user/login.html.twig');
    }

}
