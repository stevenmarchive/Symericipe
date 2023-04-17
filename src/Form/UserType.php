<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert; // Évite d'avoir des données corrompues


class UserType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
    ->add('fullName',TextType::class,[
        'attr'=>['class'=>'form-control','minlength'=>'2','maxlenght'=>'50'],
        'label'=>'Nom / Prénom',
        'label_attr'=>['class'=>'form_label mt-4'],
        'constraints'=>[new Assert\Length(['min'=>2,'max'=>50])]])
    
    ->add('pseudo',TextType::class,[
        'required'=> false,
        'attr'=>['class'=>'form-control','minlength'=>'2','maxlenght'=>'50'],
        'label'=>'pseudo (Facultatif)',
        'label_attr'=>['class'=>'form_label mt-4'],
        'constraints'=>[new Assert\Length(['min'=>2,'max'=>50])]])

    ->add('password',PasswordType::class,[
        'attr'=>['class'=>'form-control'],
        'label'=>'modification du mot de passe',
        'label_attr'=>['class'=>'form-label mt-4'],
    ])

    ->add('submit',SubmitType::class,[
        'attr'=>['class'=>'btn btn-secondary mt-4'],
        'label' => 'Modifier']);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults(['data_class' => User::class,]);
}
}
