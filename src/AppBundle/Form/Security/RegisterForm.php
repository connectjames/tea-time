<?php

namespace AppBundle\Form\Security;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupName', TextType::class, [
                'required'  => true,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'My Tea Group Name - ie: Tea Ninjas'
                ),
            ])
            ->add('email', EmailType::class, [
                'required'  => true,
                'attr' => array(
                    'placeholder' => 'john.doe@gmail.com'
                ),
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required'  => true,
            ])
            ->add('firstName', TextType::class, [
                'required'  => true,
                'attr' => array(
                    'placeholder' => 'John'
                ),
            ])
            ->add('lastName', TextType::class, [
                'required'  => true,
                'attr' => array(
                    'placeholder' => 'Doe'
                ),
            ])
            ->add('preferences', TextType::class, [
                'attr' => array(
                    'placeholder' => 'Add you favorite cupa here! You will be able to add more later'
                ),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}