<?php

namespace App\Form;

use App\Entity\RegistrationType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('fullName', TextType::class)
            ->add('phoneNumber', TextType::class)
            ->add('plainPassword', PasswordType::class, ['label' => 'Password'])
            ->add('registrationType', EntityType::class, [
                'class'=> RegistrationType::class,
                'expanded' => true
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false,
        ));

    }


    public function getBlockPrefix()
    {
        return null;
    }
}
