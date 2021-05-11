<?php

namespace App\DataFixtures;

use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $status = new Status();
        $status->setName('open');
        $manager->persist($status);

        $user = new User();
        $user->setEmail('spacebar133%d@example.com');
        $user->setRoles(['ROLE_SECOND_LINE_AGENT']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'new_password'));
        $user->setFirstName('Julio');
        $user->setLastName('Texeira');
        $user->setNumberClosedTickets(2);
        $user->setNumberReopenedTickets(1);
        $manager->persist($user);

        $user1 = new User();
        $user1->setEmail('eliacools@example.com');
        $user1->setRoles(['ROLE_CUSTOMER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'new_password2'));
        $user1->setFirstName('Elia');
        $user1->setLastName('Cools');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('martedeleeuw@hotmail.com');
        $user2->setRoles(['ROLE_FIRST_LINE_AGENT']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'new_password3'));
        $user2->setFirstName('Marte');
        $user2->setLastName('De Leeuw');
        $user2->setNumberClosedTickets(10);
        $user2->setNumberReopenedTickets(3);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('jenifer@gmail.com');
        $user3->setRoles(['ROLE_MANAGER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'new_password4'));
        $user3->setFirstName('Jenifer');
        $user3->setLastName('Bucheli');
        $manager->persist($user3);

        for ($i = 0; $i<20; $i++){
            $createdAt = new \DateTime();
            $time = $createdAt->modify('+'.$i.' hours');

            $ticket = new Ticket();
            $ticket->setTitle('This is ticket '.$i);
            $ticket->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad animi asperiores beatae commodi est facere laboriosam laudantium maiores, minima odit officiis quas quibusdam quidem quis quisquam saepe sapiente unde veritatis?');
            $ticket->setPriority('medium');
            $ticket->setIsEscalated(false);
            $ticket->setDateCreated($time);
            $ticket->setCreatedBy($user1);
            $ticket->setStatus($status);
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
