<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{

    #[Route('/task/{id}/{statut}/add', name: 'project_task_add', methods: ['GET', 'POST'])]
    #[IsGranted('acces_projet', subject: 'project')]
    public function taskFormAdd(Request $request, ?Project $project, int $statut, EntityManagerInterface $entityManager): Response
    {

        if (!$project) {
            throw $this->createNotFoundException('No project found for id ' . $request->get('id'));
        }
    
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['status' => $statut]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('TASK_EDIT', $project);
            $task->setProject($project);
            $entityManager->persist($task);
            $entityManager->flush();
    
            return $this->redirectToRoute('project_id', ['id' => $project->getId()]);
        }
    
        return $this->render('task/form.html.twig', [
            'id' => $project->getId(),
            'project' => $project,
            'taskId' => $task->getId(),
            'statut' => $statut,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/task/{id}/{taskId}/edit', name: 'project_task_edit', methods: ['GET', 'POST'])]
    #[IsGranted('acces_projet', subject: 'project')]
    public function taskFormEdit(request $request, ?Project $project, int $taskId, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $task = $taskRepository->find($taskId);
        if (!$task) {
            throw $this->createNotFoundException('No task found');
        }

        $form = $this->createForm(TaskType::class, $task, ['status' => $task->getStatus(), 'is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('TASK_EDIT', $project);
            $entityManager->flush();

            return $this->redirectToRoute('project_id', ['id' => $project->getId()]);
        }

        return $this->render('task/form.html.twig', [
            'id' => $project->getId(),
            'project' => $project,
            'taskId' => $taskId,
            'statut' => $task->getStatus(),
            'form' => $form->createView()
        ]);
    }
    #[Route('/task/{id}/{taskId}/remove', name: 'project_task_remove', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function taskRemove(Project $project, int $taskId, TaskRepository $repository, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $project);
        $task = $repository->find($taskId);

        if (!$task || $task->getProject()->getId() != $project->getId()) {
            throw $this->createNotFoundException('Task not found or does not belong to the specified project');
        }

        $entityManagerInterface->remove($task);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('project_id', ['id' => $project->getId()]);
    }
}
