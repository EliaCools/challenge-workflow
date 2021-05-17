<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;


use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use MongoDB\BSON\Timestamp;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
    private $user;

    public function __construct(CreatedByDataTransformer $transformer, Security $security,
                                StatusDataTransformer $statusTransformer, StatusRepository $statusRepository)
    {
        $this->transformer = $transformer;
        $this->security = $security;
        $this->statusTransformer = $statusTransformer;
        $this->statusRepository = $statusRepository;
        $this->user = $security->getUser();
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('description')
            ->add('priority', HiddenType::class,[
                'data' => 'unspecified'
            ])
            ->add('isEscalated', HiddenType::class,[
                'data' => 0
            ])

            ->add('wontFix', CheckboxType::class,[
                'mapped' => false,
                'required' => false,
                'label' => 'Won\'t fix'
            ])


        ->add('Save', SubmitType::class);


        // important-----------
        if(!in_array('ROLE_MANAGER',$this->user->getRoles())){
            $builder->remove('wontFix');
        }

    }




    public function OpenStatusId(): string{
        $statusName = $this->statusRepository->findBy(['name' => 'wont_fix']);
        return $statusName[0]->getId();
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
