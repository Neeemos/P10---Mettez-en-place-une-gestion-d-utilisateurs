<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Entity\Project;

class BookCreatorVoter extends Voter
{
    public function __construct(
        private ProjectRepository $projetRepository,
        private TaskRepository $tacheRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
  
        
        return $attribute === 'acces_projet' && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
      
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérification des rôles
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        /** @var Project $project */
        $project = $subject;  // Le sujet est le projet qu'on passe dans le contrôleur

        if ($isAdmin) {
            // Si l'utilisateur est admin, il a accès à tous les projets
            return true;
        }

        // Vérifie si l'utilisateur est associé au projet (par exemple via une collection 'employes' ou 'users')
        return $project->getUsers()->contains($user);  // 
    }
}
