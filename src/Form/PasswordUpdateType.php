<?php

namespace App\Form;

use App\Form\Config\GetConfigType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends GetConfigType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfigurationForm("Ancien mot de passe", "Ancien mot de passe"))
            ->add('newPassword', PasswordType::class, $this->getConfigurationForm("Nouveau mot de passe", "Nouveau mot de passe ..."))
            ->add('confirmPassword', PasswordType::class, $this->getConfigurationForm("Confirmation nouveau mot de passe", "Confirmez votre nouveau mot de passe ..."));;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
