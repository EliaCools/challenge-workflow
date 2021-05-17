<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;


use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use MongoDB\BSON\Timestamp;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    private $transformer;
    private $security;
    private $statusTransformer;
    private $statusRepository;
    private $userRepository;

    public function __construct(CreatedByDataTransformer $transformer, Security $security,
                                StatusDataTransformer $statusTransformer,
                                StatusRepository $statusRepository, UserRepository $userRepository)
    {
        $this->transformer = $transformer;
        $this->security = $security;
        $this->statusTransformer = $statusTransformer;
        $this->statusRepository = $statusRepository;
        $this->userRepository = $userRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('description', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'low' => 'low',
                    'medium' => 'medium',
                    'high' => 'high'
                ]
            ])
            ->add('isEscalated', HiddenType::class,[
                'data' => 0
            ])

            ->add('assignedTo', EntityType::class, [
                'placeholder' => ' ',
                'required' => 'false',
                'class' => User::class,
                'choices' => $this->userRepository->findByRole('ROLE_EMPLOYEE'),
            ])
          //  ->add('createdBy', HiddenType::class,[
          //      'data'=> $this->security->getUser(),
//
          // ])
            ->add('status', HiddenType::class,[
                'empty_data' => $this->OpenStatusId()
            ])

         ->add('Save', SubmitType::class);

       // $builder->get('createdBy')
       //     ->addModelTransformer($this->transformer);
        $builder->get('status')
            ->addModelTransformer($this->statusTransformer);
      //  $builder->get('dateCreated')
      //      ->addModelTransformer($this->dateDataTransformer);
    }

    public function OpenStatusId(): string{
        $statusName = $this->statusRepository->findBy(['name' => 'open']);
        return $statusName[0]->getId();
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
