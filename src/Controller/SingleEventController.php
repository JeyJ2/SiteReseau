<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SingleEventController extends AbstractController
{
    /**
     * @Route("/single/event/{id}", name="app_single_event")
     */
    public function index($id, EntityManagerInterface $manager): Response
    {   
        $event = $manager->getRepository(Event::class)->find($id);
        $inscrits = $event->getUsers();



        return $this->render('single_event/index.html.twig', [
            'event' => $event,
            'inscrits' => $inscrits,
        ]);
    }


    /**
     * @Route("/single/basket/remove/{id}", name="app_single_event_remove")
     */
    public function remove($id, EntityManagerInterface $manager)
    {   
        $event = $manager->getRepository(Event::class)->find($id);
        $manager ->remove($event);
        $manager ->flush();
        return $this->redirectToRoute('app_home');
    }
}
