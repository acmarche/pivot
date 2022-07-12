<?php

namespace AcMarche\Pivot\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class TypeOffreSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                SearchType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                ]
            );
    }
}
