<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Config\GetConfigType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ProfileUserFormType extends GetConfigType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email', EmailType::class, $this->getConfigurationForm('Email', 'Votre email'))
            ->add('first_name', TextType::class, $this->getConfigurationForm('Prénom', 'Votre prénom'))
            ->add('last_name', TextType::class, $this->getConfigurationForm('Nom', 'Votre nom'))
            ->add('billing_address', TextType::class, $this->getConfigurationForm('Adresse', 'Votre adresse'))
            ->add('city', TextType::class, $this->getConfigurationForm('Ville', 'Votre ville'))
            ->add('postal_code', TextType::class, $this->getConfigurationForm('Code postal', 'Votre code postal'))
            ->add('phone_number', TextType::class, $this->getConfigurationForm('Téléphone', 'Votre téléphone'))
            ->add('picture', FileType::class, $this->getConfigurationForm('Image de profil', 'Choisissez une image pour votre profil', [
                'mapped' => false,
                'required' => false
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
