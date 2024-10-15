<?php

namespace App\Security\Voter;

use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    const ACCESS = 'acces_projet';
    const TASK_ADD = 'TASK_ADD';
    const TASK_EDIT = 'TASK_EDIT';
    const TASK_DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        // Vérifie que l'attribut est supporté et que le sujet est une instance de Project
        return in_array($attribute, [self::ACCESS, self::TASK_ADD, self::TASK_EDIT, self::TASK_DELETE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, $project, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas authentifié, refuser l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifier si l'utilisateur est un administrateur
        if ($this->isAdmin($token)) {
            return true;
        }

        // Vérifier si l'utilisateur a accès au projet (utilisé pour l'attribut 'acces_projet' et les autres actions)
        switch ($attribute) {
            case self::ACCESS:
            case self::TASK_ADD:
            case self::TASK_EDIT:
                return $this->canAccessProject($user, $project);
            case self::TASK_DELETE:
                return false; // Par défaut, seuls les admins peuvent supprimer des tâches
        }

        return false;
    }

    private function canAccessProject(UserInterface $user, Project $project): bool
    {
        // Vérifie si l'utilisateur est associé au projet (par exemple via une relation 'users' dans l'entité Project)
        return $project->getUsers()->contains($user);
    }

    private function isAdmin(TokenInterface $token): bool
    {
        return in_array('ROLE_ADMIN', $token->getRoleNames(), true);
    }
}
