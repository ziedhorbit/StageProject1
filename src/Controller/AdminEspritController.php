<?php

namespace App\Controller;

use App\Entity\AdminEsprit;
use App\Form\AdminEspritType;
use App\Repository\AdminEspritRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/esprit')]
class AdminEspritController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/', name: 'app_admin_esprit_index', methods: ['GET'])]
    public function index(AdminEspritRepository $adminEspritRepository): Response
    {
        return $this->render('admin_esprit/index.html.twig', [
            'admin_esprits' => $adminEspritRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_esprit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adminEsprit = new AdminEsprit();
        $form = $this->createForm(AdminEspritType::class, $adminEsprit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Crypter le mot de passe
             $encodedPassword = $this->passwordEncoder->encodePassword($adminEsprit, $adminEsprit->getPassword());
             $adminEsprit->setPassword($encodedPassword);
 
             // Enregistrer l'entité
            $entityManager->persist($adminEsprit);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_esprit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_esprit/new.html.twig', [
            'admin_esprit' => $adminEsprit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_esprit_show', methods: ['GET'])]
    public function show(AdminEsprit $adminEsprit): Response
    {
        return $this->render('admin_esprit/show.html.twig', [
            'admin_esprit' => $adminEsprit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_esprit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminEsprit $adminEsprit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminEspritType::class, $adminEsprit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_esprit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_esprit/edit.html.twig', [
            'admin_esprit' => $adminEsprit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_esprit_delete', methods: ['POST'])]
    public function delete(Request $request, AdminEsprit $adminEsprit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminEsprit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adminEsprit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_esprit_index', [], Response::HTTP_SEE_OTHER);
    }
}
