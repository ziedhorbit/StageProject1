<?php

namespace App\Controller;

use App\Entity\AdminFac;
use App\Form\AdminFacType;
use App\Repository\AdminFacRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/fac')]
class AdminFacController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/', name: 'app_admin_fac_index', methods: ['GET'])]
    public function index(AdminFacRepository $adminFacRepository): Response
    {
        return $this->render('admin_fac/index.html.twig', [
            'admin_facs' => $adminFacRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_fac_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adminFac = new AdminFac();
        $form = $this->createForm(AdminFacType::class, $adminFac);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Crypter le mot de passe
             $encodedPassword = $this->passwordEncoder->encodePassword($adminFac, $adminFac->getPassword());
             $adminFac->setPassword($encodedPassword);
 
             // Enregistrer l'entité
            $entityManager->persist($adminFac);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_fac_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_fac/new.html.twig', [
            'admin_fac' => $adminFac,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_fac_show', methods: ['GET'])]
    public function show(AdminFac $adminFac): Response
    {
        return $this->render('admin_fac/show.html.twig', [
            'admin_fac' => $adminFac,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_fac_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminFac $adminFac, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminFacType::class, $adminFac);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_fac_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_fac/edit.html.twig', [
            'admin_fac' => $adminFac,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_fac_delete', methods: ['POST'])]
    public function delete(Request $request, AdminFac $adminFac, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminFac->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adminFac);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_fac_index', [], Response::HTTP_SEE_OTHER);
    }
}
