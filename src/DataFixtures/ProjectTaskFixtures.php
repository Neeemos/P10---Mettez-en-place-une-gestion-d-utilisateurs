<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectTaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Assuming you already have some projects and users in the database
        $projects = $manager->getRepository(Project::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($projects as $project) {
            for ($i = 0; $i < rand(5, 10); $i++) { // Generate between 5 to 15 tasks per project
                $task = new Task();
                $task->setTitle($faker->sentence);
                $task->setDescription($faker->paragraph);
                $task->setDate($faker->dateTimeThisYear);
                $task->setProject($project);
                $task->setStatus(rand(0, 3)); // Random status between 0 and 3
                $user = $users[array_rand($users)];
                $task->setUser($user);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}
