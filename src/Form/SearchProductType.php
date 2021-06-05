<?php

namespace App\Form;

use App\Form\Config\GetConfigType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProductType extends GetConfigType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('words', SearchType::class, $this->getConfigurationForm('Recherche', 'Tapez le nom d\'un produit ou un mot clé'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
