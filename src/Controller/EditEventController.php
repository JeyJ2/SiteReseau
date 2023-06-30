<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EditEventController extends AbstractController
{
    /**
     * @Route("/edit/event/{id}/admin", name="app_edit_event")
     */
    public function index($id, EntityManagerInterface $manager, Request $request, SluggerInterface $slugger): Response
    {   
        $event = $manager->getRepository(Event::class)->find($id);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imgEvent = $form->get('picture')->getData();
            if($imgEvent){
                $originalFilename = pathinfo($imgEvent->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgEvent->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgEvent->move(
                        $this->getParameter('event_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    dd($e->getMessage()."problème lors de téléchargement");
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setPicture($newFilename);
            }
            $manager->persist($event);
            $manager->flush();
            $notification = new Notification();
            $notification->setCreatedAt(new \DateTime());
            $notification->setMessage("attention : On a modifié une des informations de l'événement ".$event->getTitle()." ".$event->getCity());
            $users = $event->getUsers();
            foreach ($users as $user) {
                $notification->addUser($user);
            }
            $manager->persist($notification);
            $manager->flush();
            return $this->redirectToRoute('app_single_event', ['id' => $event->getId()]);
        }


        return $this->render('edit_event/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
