<?php

namespace App\Controller;

use Date;
use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Service\Mailjet;
use App\Entity\Notification;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AddEventController extends AbstractController
{
    /**
     * @Route("/add/event/admin", name="app_add_event")
     */
    public function index(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, Mailjet $mailjet): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imgEvent = $form->get('picture')->getData();
            if(!$imgEvent){
                $this->addFlash('danger', 'Vous devez choisir un fichier');
                $form = $this->createForm(EventType::class, $event);
            }
            if ($imgEvent) {
                $originalFilename = pathinfo($imgEvent->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgEvent->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgEvent->move(
                        $this->getParameter('event_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    dd($e->getMessage() . "problème lors de téléchargement");
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setPicture($newFilename);
                $manager->persist($event);
                $manager->flush();
                $this->addFlash('success', 'L\'événement a bien été ajouté');
                $users = $manager->getRepository(User::class)->findAll();
                $notification = new Notification();
                $date = $event->getDate()->format('Y-m-d');
                $message = "un nouveux événement a été ajouté".$event->getTitle()." à ".$event->getCity()." le ".$date;
                $notification->setCreatedAt(new DateTime());
                $notification->setMessage($message);
                foreach($users as $user){
                    $notification->addUser($user);
                    $user->addNotification($notification);
                    //$mailjet->sendEmail($user , $message);
                }
                $manager->persist($notification);
                $manager->flush();
                $manager->persist($user);
                $manager->flush();
                $form = $this->createForm(EventType::class, new Event());
            }
        }

        return $this->render('add_event/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
