<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $post = new Post();
            $post->setTitle("Harry Potter 20th anniversary: The UK film locations");
            $post->setContent("It's 20 years since the boy wizard Harry Potter hit the big screen. In November 2001, Harry Potter and the Philosopher's Stone was released in cinemas and it was cool for adults to be seen on beaches, buses or trains with their heads in one of the phenomenally-popular children's books. However, no one location would do to bring the wizarding magic to life when the novels were made into films.  A number of locations including cathedrals, a castle and places of historic interest across England soon had new claims to fame.");
            $post->setUser($this->getReference(sprintf("user%s", rand(1, 3))));
            $manager->persist($post);
        }
        $manager->flush();
    }

    /**
     * @return \DateTime
     */
    private function getRundomDate()
    {
        $start = strtotime("2020-10-01 00:00:00");
        $end =  time();
        return new \DateTimeImmutable(date("Y-m-d H:i:s", rand($start, $end)));
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
