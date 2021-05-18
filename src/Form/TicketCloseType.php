<?php


namespace App\Form;

use App\Entity\Status;
use App\Entity\Ticket;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TicketCloseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('close', SubmitType::class, [
                'label' => 'Close Ticket',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}