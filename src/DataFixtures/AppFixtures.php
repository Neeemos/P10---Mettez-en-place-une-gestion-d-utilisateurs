<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private UserFactory $userFactory;

    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(50);
        ProjectFactory::createMany(10);
        TaskFactory::createMany(50);
        $admin = $this->userFactory->createAdmin();
        $manager->persist($admin);

        $manager->flush();

        $projectUserFixture = new ProjectUserFixtures();
        $projectUserFixture->load($manager);
    }
}
