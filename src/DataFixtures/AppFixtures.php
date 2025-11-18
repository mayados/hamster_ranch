<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->createUser('user@sf.com',"motdepasse",['ROLE_USER']);
        $admin = $this->createUser('adminhr@sf.com',"motdepasse",['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->persist($admin);
        $manager->flush();
    }

    public function createUser(string $email, string $password, array $roles): User {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        return $user;
    }
}
