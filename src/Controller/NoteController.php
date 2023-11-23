<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Matiere;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class NoteController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/note', name: 'app_note')]
    public function index(Request $request, TranslatorInterface $translator)
    {
        $allnotes = $this->em->getRepository(Note::class)->findAll();
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);

        $matiere = $this->em->getRepository(Matiere::class)->findAll();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($note);
            $this->em->flush();
            $this->addFlash('success', $translator->trans('flash.added'));
            return $this->redirectToRoute('app_note');
        }/* elseif ($request->isMethod('POST')) {
            $this->addFlash('error', $translator->trans('flash.Notdelete'));
        } */

        return $this->render('note/index.html.twig', [
            'controller_name' => 'NoteController',
            'notes' => $allnotes,
            'matieres' => $matiere,
            'form' => $form->createView()
        ]);
    }


}
