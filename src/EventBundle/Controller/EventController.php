<?php

namespace EventBundle\Controller;

use EventBundle\Entity\Club;
use EventBundle\Entity\Event;
use EventBundle\Entity\Participation;
use EventBundle\EventBundle;
use EventBundle\Form\EventType;
use EventBundle\Repository\EventRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EventController extends Controller
{
    public function afficheEventAction(Request $request)
    {   

        //dump(get_class($paginator));
        $participations = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findAll();
        $clubs = $this->getDoctrine()->getManager()->getRepository(Club::class)->findAll();
        $eventsr = $this->getDoctrine()->getManager()->getRepository(Event::class)->findAll();
    
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate(
            $eventsr,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)

        );
        return $this->render('@Event/Event/afficheEvent.html.twig', array('formations' => $events , "nbre"=>count($events),"nbrp"=>count($participations),"nbrc"=>count($clubs)));
    }

    public function ajoutEventAction(Request $request)
    {
        $formation = new Event();
        $form = $this->createForm(EventType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            /** @var UploadedFile $file */
            $file = $formation->getImage();
            $filename = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            $file->move($this->getParameter('photos_directory'), $filename);
            $formation->setImage($filename);
            $em->persist($formation);//persister les donner dans la base de donnee
            $em->flush();//tlansi kif el commit
            return $this->redirectToRoute('event_afficheEvent');
        }
        return $this->render('@Event/Event/ajoutEvent.html.twig', array('form' => $form->createView()));
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    public function supprimeEventAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('event_afficheEvent');
    }

    public function modifieEventAction(Request $request,$id){

        $club= new Event();
        $em=$this->getDoctrine()->getManager();
        $club=$em->getRepository(Event::class)->find($id);
        $form=$this->createForm(EventType::class,$club);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $em=$this->getDoctrine()->getManager();
            /** @var UploadedFile $file */
            $file = $club->getImage();
            $filename = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move($this->getParameter('photos_directory'), $filename);
            $club->setImage($filename);
            $em->flush();
            return $this->redirectToRoute('event_afficheEvent');
        }

        return $this->render('@Event/Event/modifieEvent.html.twig', array('form' => $form->createView()));

    }

    public function UserEventAction()
    {
        $em = $this->getDoctrine();
        $tab = $em->getRepository(Event::class)->findAll();
        return $this->render('@Event/Event/UserEvent.html.twig', array('events' => $tab));
    }
    public function  navigAction(){
        $formations = $this->getDoctrine()->getManager()->getRepository(Event::class)->findAll();
        $formationspart = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findBy(["iduser"=> $this->getUser()->getId()]);
       
        return $this->render('@Event/Event/navig.html.twig',array('formations' => $formations , 'fparticipation'=> $formationspart ));
    }


    public function searchAction(Request $request){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizer = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizer, $encoders);

        $event = $request->request->get("event");
        $em = $this->getDoctrine()->getManager();
    
        $sql = "SELECT `*` FROM `event` WHERE `nom_event` LIKE '%".$event."%'";
        $result = $em->getConnection()->prepare($sql);
        $result->execute();
        $events = $result->fetchAll();
        //$events = $this->getDoctrine()->getRepository(Event::class)->findAll(); 
       
        $jsonContent = $serializer->serialize($events, 'json');
        return new Response($jsonContent);

    }

    public function searchdateAction(Request $request){


            $date = $request->request->get("dateevent");
            $eventsr = $this->getDoctrine()->getManager()->getRepository(Event::class)->findby(array('dateevent'=> new \DateTime($date) ));
            $paginator = $this->get('knp_paginator');
            $events = $paginator->paginate(
                $eventsr,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',5)
    
            );
            return $this->render('@Event/Event/afficheEvent.html.twig', array('formations' => $events));
    }
    public function recentsEventsAction(Request $request){
        $date = date('Y-m-d');
        $em = $this->getDoctrine()->getManager();
    
        $sql = "SELECT `*` FROM `event` WHERE `DateEvent` < '$date' ";
        $result = $em->getConnection()->prepare($sql);
        $result->execute();
        $eventsr = $result->fetchAll();
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate(
            $eventsr,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)

        );
        return $this->render('@Event/Event/Events.html.twig', array('formations' => $events));

    }
    public function venirEventsAction(Request $request){
        $date = date('Y-m-d');
        $em = $this->getDoctrine()->getManager();
    
        $sql = "SELECT `*` FROM `event` WHERE `DateEvent` >= '$date' ";
        $result = $em->getConnection()->prepare($sql);
        $result->execute();
        $eventsr = $result->fetchAll();
        
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate(
            $eventsr,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)

        );
        return $this->render('@Event/Event/Events.html.twig', array('formations' => $events));

    }
}