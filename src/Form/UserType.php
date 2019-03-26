<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    private $passwordEncoderInterface;

    public function __construct(UserPasswordEncoderInterface $passwordEncoderInterface)
    {
        $this->passwordEncoderInterface	= $passwordEncoderInterface;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('password', TextType::class)
            ->add('discordId', TextType::class, ['required' => false, 'empty_data' => 'null'])
        ;
        
        $builder->get('password')
            ->addModelTransformer(new CallbackTransformer(
                function ($password) {
                    return $this->passwordEncoderInterface->encodePassword(new User, $password);
                },
                function ($password) {
                    return $this->passwordEncoderInterface->encodePassword(new User, $password);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
