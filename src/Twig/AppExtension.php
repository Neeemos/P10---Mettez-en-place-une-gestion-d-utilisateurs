<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('initials', [$this, 'getInitials']),
        ];
    }

    public function getInitials(string $firstName, string $lastName): string
    {
        $initials = '';

        if (!empty($firstName)) {
            $initials .= strtoupper($firstName[0]) . '.';
        }

        if (!empty($lastName)) {
            $initials .= strtoupper($lastName[0]);
        }

        return $initials;
    }
}
