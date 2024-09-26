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

    #[Route('/', name: 'project_index', methods: ['GET'])]
    public function index(request $request, ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'projects' => $projects
        ]);
    }


    #[Route('/project/form/', name: 'project_form', methods: ['GET', 'POST'])]
    public function projectFormAdd(request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }
        return $this->render('project/form.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'form' => $form
        ]);
    }
    #[Route('/project/form/{id}', name: 'project_form_id',  methods: ['GET', 'POST']) ]
    public function projectFormEdit(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/form.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/project/remove/{id}', name: 'project_remove',  methods: ['GET', 'POST'])]
    public function projectRemove(Project $project, EntityManagerInterface $entityManagerInterface): Response
    {
        if (!$project) {
            throw $this->createNotFoundException('Car not found');
        }
        $entityManagerInterface->remove($project);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('project_index');
    }

    #[Route('/project/manager/{id}', name: 'project_id',  methods: ['GET', 'POST'])]
    public function projectFind(request $request, Project $project, TaskRepository $TaskRepository): Response
    {
        $tasks = $TaskRepository->findBy(['project' => $project->getId()]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        return $this->render('project/project.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'id' => $project->getId(),
            'project' => $project,
            'tasks' => $tasks
        ]);
    }
}
