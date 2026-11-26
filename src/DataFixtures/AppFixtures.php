<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Compte administrateur de développement (environnement dev uniquement).
 *   php bin/console doctrine:fixtures:load --append
 */
class AppFixtures extends Fixture
{
    public const DEV_USERNAME = 'dev_admin';
    public const DEV_PASSWORD = 'DevAdmin2026';

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new AppUser())
            ->setUsername(self::DEV_USERNAME)
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, self::DEV_PASSWORD));

        $manager->persist($user);
        $manager->flush();
    }
}
