<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function index(EntityManagerInterface $manager): Response
    {   
        $user = $this->getUser();
        $events = $user->getEvents();
        $notifications = $user->getNotifications();


        return $this->render('profile/index.html.twig', [
            'events' => $events,
            'notifications' => $notifications,
        ]);
    }

    /**
     * @Route("/profile/event/{id}", name="app_profile_event")
     */
    public function participer($id, EntityManagerInterface $manager): Response
    {
        $event= $manager->getRepository(Event::class)->find($id);
        $user = $this->getUser();
        $event->addUser($user);
        $manager->persist($event);
        $manager->flush();
        $user->addEvent($event);
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('app_profile');

    }
}
