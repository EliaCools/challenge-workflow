<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles',ChoiceType::class,[
                'choices'  => [
                    'first line agent' => 'ROLE_FIRST_LINE_AGENT',
                    'second line agent' => 'ROLE_SECOND_LINE_AGENT',
                ],
            ])
            ->add('plainPassword', HiddenType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'data' => 'TemporaryPass123',
                'mapped' => false,
            ])
            ->add('firstName')
            ->add('lastName');


            $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($tagsAsArray) {
                        // transform the array to a string
                        return implode(', ', $tagsAsArray);
                        },
                    function ($tagsAsString) {
                        // transform the string back to an array
                        return explode(', ', $tagsAsString);
                    }
                    ))
                    ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
