<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(EntityManagerInterface $manager): Response
    {   
        $events = $manager->getRepository(Event::class)->findAll();


        return $this->render('home/index.html.twig', [
            'events' => $events,
        ]);
    }
}
