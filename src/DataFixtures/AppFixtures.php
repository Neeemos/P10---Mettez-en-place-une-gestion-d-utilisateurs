<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(50);
        ProjectFactory::createMany(10);
        TaskFactory::createMany(50);
        $manager->flush();
        
        $projectUserFixture = new ProjectUserFixture();
        $projectUserFixture->load($manager);
    }
}
