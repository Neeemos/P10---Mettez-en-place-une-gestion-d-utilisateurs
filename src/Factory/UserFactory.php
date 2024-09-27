<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $plainPassword = self::faker()->text(10); // Generate a random plain password
        return [
            'date' => self::faker()->dateTime(),
            'email' => self::faker()->email(),
            'name' => self::faker()->text(10),
            'password' => $this->userPasswordHasher->hashPassword(new User(), $plainPassword),
            'status' => self::faker()->randomElement(['CDI', 'CDD']),
            'surname' => self::faker()->text(10),
        ];
    }
    public function createAdmin(): User
    {
        $user = new User();
        $user->setDate($this->defaults()['date']);
        $user->setEmail("admin@admin.com");
        $user->setName("admin");
        $user->setPassword($this->userPasswordHasher->hashPassword(new User(), "admin"));
        $user->setStatus($this->defaults()['status']);
        $user->setSurname($this->defaults()['surname']);
        $user->setRoles(["ROLE_ADMIN"]);

        return $user;
    }
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
