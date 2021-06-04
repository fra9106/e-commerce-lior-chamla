<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Config\GetConfigType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends GetConfigType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, $this->getConfigurationForm('Email','Votre email') )
            ->add('password', PasswordType::class,  $this->getConfigurationForm('Mot de passe','Choisissez un mot de passe'))
            ->add('confirm_password', PasswordType::class,  $this->getConfigurationForm('Confimation de mot de passe','Confirmez votre mot de passe'))
            ->add('first_name', TextType::class, $this->getConfigurationForm('Prénom','Votre prénom'))
            ->add('last_name', TextType::class, $this->getConfigurationForm('Nom','Votre nom')) 
            ->add('billing_address', TextType::class, $this->getConfigurationForm('Adresse','Votre adresse'))
            ->add('city', TextType::class, $this->getConfigurationForm('Ville', 'Votre ville'))
            ->add('postal_code', TextType::class, $this->getConfigurationForm('Code postal','Votre code postal'))
            ->add('phone_number', TextType::class, $this->getConfigurationForm('Téléphone','Votre téléphone'))
            ->add('picture', FileType::class, $this->getConfigurationForm('Image de profil','Choisissez une image pour votre profil'))
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
