<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class TodoController extends AbstractController
{
    /**
     * @Route("/{_locale}/todo", name="todo")
     * @Route("/{_locale}/todo/{sort}/{order}", name="todo/")
     */
    public function index(UserInterface $user,TodoRepository $todoRepo,$sort = null,$order = null): Response
    {

            $todos = $user->getTodos();

        if($sort === "createdAt" && $order === "DESC"){
             $todos = $todoRepo->findByUserSortedByMostRecent($user);
        }
        if($sort === "createdAt" && $order === "ASC"){
            $todos =$todoRepo->findByUserSortedByLessRecent($user);
        }

        if($sort === "dueDate" && $order === "DESC"){
            $todos = $todoRepo->findByUserDueDateByMostRecent($user);
        }
        if($sort === "dueDate" && $order === "ASC"){
            $todos =$todoRepo->findByUserDueDateByLessRecent($user);
        }

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * @Route("/todo/create", name="todo_create",priority="1")
     */
    public function create(Request $req,EntityManagerInterface $manager,UserInterface $user): Response
    {
        $todo = new Todo();
        $formulaire = $this->createForm(TodoType::class,$todo);
            $formulaire->handleRequest($req);
        if($formulaire->isSubmitted() && $formulaire->isValid())
        {   $todo->setDo(0);
            $todo->setCreatedAt(new \DateTime());
            $todo->setUser($user);
            $manager->persist($todo);
            $manager->flush();
            return $this->redirectToRoute('todo') ;
        }
        return $this->render('todo/create.html.twig',[
            "formulaireTodo" => $formulaire->createView()
    ]);

    }

        /**
         *
         * @Route("/todo/delete/{id}" ,name="todo_delete",priority="1")
         *
         */
        public function delete(Todo $todo,EntityManagerInterface $manager,UserInterface $user):Response
    {
        if($user == $todo->GetUser()){
            $manager->remove($todo);
            $manager->flush();

        }
        return $this->redirectToRoute('todo');

    }

    /**
     *
     * @route("/todo/do/{id}",name="todo_do",priority="2")
     */
    public function todomake(Request $req,EntityManagerInterface $manager,Todo $todo,UserInterface $user):Response
    {
        $do = $todo->getDo();
        if($user == $todo->GetUser()){
            $do = !$do;
        $todo->setDo($do);
        $manager->persist($todo);
        $manager->flush();
        }
        return $this->redirectToRoute('todo');
    }





}
