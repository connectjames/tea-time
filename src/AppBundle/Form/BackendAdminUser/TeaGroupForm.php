<?php

namespace AppBundle\Form\BackendAdminUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TeaGroupForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required'  => true,
                'attr' => array(
                    'placeholder' => 'My Tea Group Name - ie: Tea Ninjas'
                ),
            ])
        ;
    }
}
