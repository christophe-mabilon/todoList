<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/{_locale}")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index(UserRepository $repo): Response
    {   $users = $repo->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }

    /**
     *
     * @Route("/admin/userDelete/{id}" ,name="admin_user_delete",requirements={"id":"\d+"})
     *
     */
    public function UserDelete(User $user,EntityManagerInterface $manager):Response
    {

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_index');

    }

    /**
     *
     * @Route("/admin/{id}",name="admin_user_show",requirements={"id":"\d+"})
     * @Route("/admin/todos", name="admin_todos")
     * @Route("/admin/todo/{order}", name="admin_todo_order")
     */
    public function show(User $user,TodoRepository $todoRepo,$sort = null,$order = null,PaginatorInterface $paginator,Request $req):Response
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

        return $this->render('admin/show.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $user,
            'todos' => $todos,
        ]);
    }



    /**
     *
     * @Route("/admin/todoDelete/{id}" ,name="admin_todo_delete",requirements={"id":"\d+"})
     *
     */
    public function delete(Todo $todo,EntityManagerInterface $manager):Response
    {

            $manager->remove($todo);
            $manager->flush();

        return $this->redirectToRoute('admin_index');

    }
}
