<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;

class BookCreatorVoter extends Voter
{
    public function __construct(
        private ProjectRepository $projetRepository,
        private TaskRepository $tacheRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'acces_projet' || $attribute === 'acces_tache';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérification des rôles
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        // Logique pour 'acces_projet'
        if ($attribute === 'acces_projet') {
            if ($isAdmin) {
                // Si l'utilisateur est admin, on renvoie tous les projets
                $projets = $this->projetRepository->findAll();
            } else {
                // Sinon, on renvoie uniquement les projets liés à l'utilisateur
                $projets = $this->projetRepository->findByUser($user);
            }
            return !empty($projets); // Retourne vrai si des projets sont trouvés
        }

        // Logique pour 'acces_tache'
        if ($attribute === 'acces_tache') {
            $tache = $this->tacheRepository->find($subject);
            $projet = $tache?->getProjet();

            if (!$projet) {
                return false;
            }

            if ($isAdmin) {
                // Si l'utilisateur est admin, accès autorisé à toutes les tâches
                return true;
            } else {
                // Si non-admin, vérifie si l'utilisateur est employé sur le projet de la tâche
                return $projet->getEmployes()->contains($user);
            }
        }

        return false;
    }
}
