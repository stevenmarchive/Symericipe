<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert; // Évite d'avoir des données corrompues



class RecipeType extends AbstractType
{

private $token;

public function __construct(TokenStorageInterface $token)
{
$this->token=$token;   
}

public function buildForm(FormBuilderInterface $builder, array $options): void
{

    $label = 'Créer la recette';

    if ($options['data'] && $options['data']->getId()) {
        $label = 'Modifier la recette';
    }

$builder
    ->add('name',TextType::class,[
        'attr'=>['class'=>'form-control','minlength'=>'2','maxlength'=>'50'],
        'label'=>'Nom',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\Length(['min'=>2, 'max'=>50]),new Assert\NotBlank()]])

    ->add('time',IntegerType::class,[
        'attr'=>['class'=>'form-control','min'=>1,'max'=>1440],
        'label'=>'Temps en minutes',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\Positive(), new Assert\LessThan(1441)]])

    ->add('nbPeople',IntegerType::class,[
        'attr'=>['class'=>'form-control','min'=>1,'max'=>50],
        'label'=>'Nombre de personnes',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\Positive(), new Assert\LessThan(5)]])

    ->add('difficulty',RangeType::class,[
        'attr'=>['class'=>'form-range','min'=>1,'max'=>5],
        'label'=>'Difficulté',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\Positive(), new Assert\LessThan(51)]])

    ->add('description',TextareaType::class,[
        'attr'=>['class'=>'form-control','min'=>1,'max'=>50],
        'label'=>'Description',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\NotBlank()]])

    ->add('price',MoneyType::class,[
        'attr'=>['class'=>'form-control'],
        'label'=>'Prix',
        'label_attr'=>['class'=>'form-label mt-4'],
        'constraints'=>[new Assert\Positive(),new Assert\LessThan(1001)]])
        
    ->add('ingredients',EntityType::class,[
        'class'=>Ingredient::class,
        'query_builder'=> function(IngredientRepository $r)
        {
            return $r->createQueryBuilder('i')
                ->where('i.user=:user')
                ->orderBy('i.name','ASC')
                ->setParameter('user',$this->token->getToken()->getUser());
        },
        'choice_label'=>'name',
        'label_attr'=>['class'=>'form-label mt-4'],
        'multiple'=>true,
        'expanded'=>true,
    ])

    ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn btn-secondary mt-4'],
        'label' => $label,
    ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
