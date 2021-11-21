<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    /**
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail(sprintf("test%s@gmail.com", $i));
            $password = $this->encoder->hashPassword($user, 'asdfasdf');
            $user->setPassword($password);
            $this->setReference(sprintf("user%s", $i), $user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
