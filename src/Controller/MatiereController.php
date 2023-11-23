<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MatiereController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/matiere', name: 'app_matiere')]
    public function index(Request $request)
    {
        $allmatiere = $this->em->getRepository(Matiere::class)->findAll();
        $new_matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $new_matiere);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_matiere);
            $this->em->flush();
        }

        return $this->render('matiere/index.html.twig', [
            'controller_name' => 'MatiereController',
            'allmatiere' => $allmatiere,
            'form' => $form->createView()
        ]);
    }

    #[Route('/matiere/add', name: 'matiere_add')]
    public function add(Request $request, TranslatorInterface $translator)
    {
        $new_matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $new_matiere);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($new_matiere);
            $this->em->flush();
            $this->addFlash('success', $translator->trans('flash.Matsuccess'));
            return $this->redirectToRoute('matiere_add');
        }/* elseif ($request->isMethod('POST')) {
            $this->addFlash('error', $translator->trans('flash.Materror'));
        } */

        return $this->render('matiere/add.html.twig', [
            'controller_name' => 'MatiereController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/matiere/{id}', name: 'matiere_details')]
    public function details(Matiere $matiere = null, Request $request, TranslatorInterface $translator)
    {
        if($matiere === null){
            return $this->redirectToRoute('app_matiere');
        }

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($matiere);
            $this->em->flush();
            if($form){
                $this->addFlash('success', $translator->trans('flash.success'));
                return $this->redirectToRoute('matiere_details', ['id' => $matiere->getId()]);
            }/* elseif ($request->isMethod('POST')) {
                $this->addFlash('error', 'Une erreur est survenue');
            } */
        }
        
        return $this->render('matiere/details.html.twig', [
            'controller_name' => 'MatiereController',
            'allmatiere' => $matiere,
            'form' => $form->createView()
        ]);
    }

    #[Route('/matiere/delete/{id}', name: 'matiere_delete')]
    public function delete(Matiere $matiere, TranslatorInterface $translator)
    {
        if($matiere === null){
            return $this->redirectToRoute('app_matiere');
        }

        $this->em->remove($matiere);
        $this->em->flush();
        $this->addFlash('success', $translator->trans('flash.delete'));
        return $this->redirectToRoute('app_matiere');
    }

}
