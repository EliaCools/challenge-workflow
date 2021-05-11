<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //create 2 ROLE_CUSTOMERS

        $user = new User();
        $user->setEmail('spacebar133%d@example.com');
        $user->setRoles(['ROLE_SECOND_LINE_AGENT']);
        $user->setPassword('chao01232');
        $user->setFirstName('Julio');
        $user->setLastName('Texeira');
        $user->setNumberClosedTickets(2);
        $user->setNumberReopenedTickets(1);
        $manager->persist($user);
        $manager->flush();

        $user2 = new User();
        $user2->setEmail('martedeleeuw@hotmail.com');
        $user2->setRoles(['ROLE_FIRST_LINE_AGENT']);
        $user2->setPassword('becode123');
        $user2->setFirstName('Marte');
        $user2->setLastName('De Leeuw');
        $user2->setNumberClosedTickets(10);
        $user2->setNumberReopenedTickets(3);
        $manager->persist($user2);
        $manager->flush();

        $user3 = new User();
        $user3->setEmail('jenifer@gmail.com');
        $user3->setRoles(['ROLE_MANAGER']);
        $user3->setPassword('becode123');
        $user3->setFirstName('Jenifer');
        $user3->setLastName('Bucheli');
        $user3->setNumberClosedTickets(null);
        $user3->setNumberReopenedTickets(null);
        $manager->persist($user3);
        $manager->flush();
    }
}
