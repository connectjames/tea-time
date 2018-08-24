<?php

namespace AppBundle\Form\BackendAdminUser;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required'  => true,
                'attr' => array(
                    'placeholder' => 'john.doe@gmail.com'
                ),
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required'  => true,
            ])
            ->add('level', RangeType::class, [
                'attr' => array(
                    'value' => 50
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
