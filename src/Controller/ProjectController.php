<?php

namespace App\Controller;

use App\Form\ProjectType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProjectRepository;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class ProjectController extends AbstractController
{

    #[Route('/index', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
   
        // Vérification des rôles
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Si l'utilisateur est admin, on récupère tous les projets
            $projects = $projectRepository->findAll();
        } else {
            // Sinon, on récupère les projets associés à l'utilisateur
            $projects = $projectRepository->findByUser($user);
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }


    #[Route('/project/form/', name: 'project_form', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function projectFormAdd(request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }
        return $this->render('project/form.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/project/form/{id}', name: 'project_form_id', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function projectFormEdit(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/remove/{id}', name: 'project_remove', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function projectRemove(Project $project, EntityManagerInterface $entityManagerInterface): Response
    {
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }
        $entityManagerInterface->remove($project);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('project_index');
    }

    #[Route('/project/manager/{id}', name: 'project_id', methods: ['GET', 'POST'])]
    #[IsGranted('acces_projet', subject: 'project')]
    public function projectFind(Project $project, TaskRepository $TaskRepository): Response
    {

        $tasks = $TaskRepository->findBy(['project' => $project->getId()]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        return $this->render('project/project.html.twig', [
            'id' => $project->getId(),
            'project' => $project,
            'tasks' => $tasks
        ]);
    }
}
