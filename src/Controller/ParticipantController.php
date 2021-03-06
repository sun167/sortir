<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\ManageEntity\UpdateEntity;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/participant", name="participant")
     */
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/participant/detail/{id}", name="participant_detail", requirements={"id"="\d+"})
     */
    public function detail($id, ParticipantRepository $participantRepository, CampusRepository $campusRepository): Response
    {

        $participant = $participantRepository->find($id);
        //$participant= $this->getUser();
        if (!$participant) {
            // throw $this -> createNotFoundException("Oops ! Ce participant n'éxiste pas !");
            return $this->redirectToRoute('sortie_list');
        }

        $campus = $campusRepository->find($id);

        return $this->render('participant/detail.html.twig', [
            "campus" => $campus,
            "participant" => $participant
        ]);
    }

    /**
     * @Route("/participant/edit/{id}", name="participant_edit")
     */
    public function edit($id,
                         EntityManagerInterface $entityManager,
                         Request $request,
                         UserPasswordEncoderInterface $passwordEncoder,
                         ParticipantRepository $participantRepository,
                         CampusRepository $campusRepository): Response
    {


        // $participant = $participantRepository->find($id);
        $participant = $this->getUser();

        $campus = $campusRepository->find($id);
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);


        if ($participantForm->isSubmitted() && $participantForm->isValid()) { // dans cet ordre là

            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $participantForm->get('password')->getData()));

            //IMAGE
            $file = $participantForm->get('urlPhoto')->getData();
            /**
             * @var UploadedFile $file
             */
            if ($file) {
                $newFileName = $participant->getNom() . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_image_participant'), $newFileName);
                $participant->setUrlPhoto($newFileName);
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil correctement modifié !!');
            return $this->redirectToRoute('sortie_list', ['id' => $participant->getId()]);
        }

        $entityManager->refresh($participant);

        return $this->render('participant/edit.html.twig', [
            "campus" => $campus,
            "participant" => $participant,
            'participantForm' => $participantForm->createView()
        ]);
    }


}
