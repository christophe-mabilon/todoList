<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @Route("/{_locale}")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("/todo", name="todo")
     * @Route("/todos/{order}", name="todo_order")
     */
    public function index(UserInterface $user,TodoRepository $todoRepo,$order = null,PaginatorInterface $paginator,Request $req): Response
    {
        if($order){
            switch ($order){
                case 'recent':
                    $todos = $todoRepo->findByUserSortedByMostRecent($user);
                    break;
                case 'oldest':
                    $todos =$todoRepo->findByUserSortedByLessRecent($user);
                    break;
                case 'urgent':
                    $todos = $todoRepo->findByUserDueDateByMostRecent($user);
                    break;
                case 'leastUrgent':
                    $todos =$todoRepo->findByUserDueDateByLessRecent($user);
                    break;
            }
        }else{
            $todos = $user->getTodos();
            }

        $todos = $paginator->paginate(
            $todos,
        $req->query->getInt('page',1),
        10);
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
         * @Route("/todo/delete/{id}" ,name="todo_delete",requirements={"id":"\d+"})
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


}
