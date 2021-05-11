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

            $user=new User();
            $user->setEmail('spacebar133%d@example.com');
            $user->setRoles(['ROLE_SECOND_LINE_AGENT']);
            $user->setPassword('chao01232');
            $user->setFirstName('Julio');
            $user->setLastName('Texeira');
            $user->setNumberClosedTickets(2);
            $user->setNumberReopenedTickets(1);
            $manager->persist($user);


        $manager->flush();
    }
}
