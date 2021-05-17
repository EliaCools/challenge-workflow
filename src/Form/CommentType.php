<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\DataTransformer\CommentToTicketUserTransformer;
use App\Form\DataTransformer\TicketDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use Cassandra\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();


            $builder
                ->setMethod('POST')
                ->add('isPublic', ChoiceType::class, [
                    'choices' => [
                      'Public' => true,
                      'Private' => false,
                    ],
                    'label'    => 'Public message?',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' =>false
                ])
                ->add('content', TextType::class, ['label' => 'Your message: '])
                ->add('Send', SubmitType::class);

        if(in_array('ROLE_CUSTOMER',$user->getRoles())){
            $builder->remove('isPublic');
        }

//            $builder
//                ->setMethod('POST')
//                ->add('content', TextType::class, ['label' => 'Your message: '])
//                ->add('isPublic', HiddenType::class, [
//                    'data' => true
//                ])
//                ->add('Send', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'roles' => ['ROLE_USER']
        ]);
    }
}
