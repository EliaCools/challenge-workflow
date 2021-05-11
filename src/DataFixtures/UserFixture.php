<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {


            $user=new User();
            $user->setEmail('spacebar133%d@example.com');
            $user->setRoles(['ROLE_SECOND_LINE_AGENT']);
            $user->setPassword($this->passwordEncoder->encodePassword($user,'new_password'));
            $user->setFirstName('Julio');
            $user->setLastName('Texeira');
            $user->setNumberClosedTickets(2);
            $user->setNumberReopenedTickets(1);
            $manager->persist($user);


        $manager->flush();
    }
}
