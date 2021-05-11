<?php

namespace App\Form;

use App\Entity\Comment;
use Cassandra\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a message',
                    ]),
                ]
            ])
            ->add('isPublic', HiddenType::class, [
                'data' => true
            ])
            ->add('createdBy', HiddenType::class, [
                UserType::class => 'John'
            ])
            ->add('ticket', TicketType::class)
            ->add('Submit', SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
