<?php
// src/Form/RobotType.php

namespace App\Form;

use App\Entity\Robot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RobotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Név',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Típus',
                'choices' => [
                    'Brawler' => 'brawler',
                    'Rouge' => 'rouge',
                    'Assault' => 'assault',
                ],
            ])
            ->add('power', IntegerType::class, [
                'label' => 'Erő',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Robot hozzáadása',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Robot::class,
        ]);
    }
}
