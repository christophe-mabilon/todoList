<?php

namespace App\DataFixtures;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TodoFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
    $user = $manager->getRepository(User::class)->find(13);

        for($i=0 ;$i < 50 ; $i++){
            $todo = new Todo();
            $todo->setDescription('Une déscription bidon');
            $todo->setCreatedAt(new \DateTime());
            $todo->setDueDate(new \DateTime('2022-07-21 07:14:06'));
            $todo->setDo(0);
            $todo ->setUser($user);
            $manager->persist($todo);
        }
        $manager->flush();
    }
}
