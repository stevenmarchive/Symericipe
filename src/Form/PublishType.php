<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PublishType extends AbstractType
{

public function buildForm(FormBuilderInterface $builder, array $options): void
{

$builder
        
    ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn btn-secondary mt-4'],
        'label' => 'Publier',]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults(['data_class' => Recipe::class,]);
}
}
