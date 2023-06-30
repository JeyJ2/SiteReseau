<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashBoardController extends AbstractController
{
    /**
     * @Route("/dash/board/admin", name="app_dash_board")
     */
    public function index(EntityManagerInterface $manager): Response
    {   
        $users = $manager->getRepository(User::class)->findAll();


        return $this->render('dash_board/index.html.twig', [
            'controller_name' => 'DashBoardController',
            'users' => $users,
        ]);
    }


    /**
     * @Route("/dash/board/delete/{id}/admin", name="app_dash_board_delete")
     */
    public function delete($id,EntityManagerInterface $manager): Response
    {   
        $user = $manager->getRepository(User::class)->find($id);
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('app_dash_board');
    }
}
