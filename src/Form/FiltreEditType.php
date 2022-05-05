<?php

namespace AcMarche\Pivot\Form;

use AcMarche\Pivot\Entity\Filtre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name_fr',
                SearchType::class,
                [
                    'label' => 'Français',
                    'help' => 'Si on veut remplacer l\'original de Hades',
                    'required' => false,
                ]
            )
            ->add(
                'name_nl',
                SearchType::class,
                [
                    'label' => 'Néerlandais',
                    'required' => false,
                ]
            )
            ->add(
                'name_en',
                SearchType::class,
                [
                    'label' => 'Anglais',
                    'required' => false,
                ]
            );
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Filtre::class,
            ]
        );
    }
}
