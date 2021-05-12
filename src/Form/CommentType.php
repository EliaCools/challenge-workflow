<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\DataTransformer\CommentToTicketUserTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    private CommentToTicketUserTransformer $transformer;
    private Ticket $ticket;

    public function __construct(CommentToTicketUserTransformer $transformer, Security $security, Ticket $ticket)
    {
        $this->transformer = $transformer;
        $this->security = $security;
        $this->ticket = $ticket;
    }

//    public function __construct(Security $security)
//    {
//
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('content', TextType::class)
            ->add('isPublic', HiddenType::class, [
                'data' => true
            ])
            ->add('createdBy', HiddenType::class, [
                'data' => $this->security->getUser(),
                'data_class' => null
            ])
            ->add('ticket')
            ->add('Submit', SubmitType::class);

        $builder->get('ticket')
            ->addModelTransformer($this->transformer);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
