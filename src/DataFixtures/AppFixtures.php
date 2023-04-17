<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

private Generator $faker;


public function __construct(){
    $this->faker = Factory::create('fr_FR');
}

public function load(ObjectManager $manager): void
{

$users=[];
for($u=0; $u<10; $u++){
    $user=new User();
    $user->setFullName($this->faker->name());
    $user->setPseudo(mt_rand(0,1) === 1 ? $this->faker->firstName : null);
    $user->setEmail($this->faker->email());
    $user->setRoles(['ROLE_USER']);
    $user->setPassword('password');
    $users[]=$user;
    $manager->persist($user);
}
    
$ingredients=[];
for ($i=0; $i<=50; $i++){
    $ingredient= new Ingredient();
    $ingredient->setName($this->faker->word());
    $ingredient->setPrice(mt_rand(0,100));
    $ingredient->setUser($users[mt_rand(0,count($users)-1)]);
    $ingredients[]=$ingredient;
    $manager->persist($ingredient);
}

$recipes=[];
for($j=0; $j<25; $j++){
    $recipe= new Recipe();
    $recipe->setName($this->faker->word());
    $recipe->setTime(mt_rand(0,1) == 1 ? mt_rand(1, 1440) : null);
    $recipe->setNbPeople(mt_rand(0,1) == 1 ? mt_rand(1, 1440) : null); 
    $recipe->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null);
    $recipe->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null);
    $recipe->setDescription($this->faker->text(300));
    $recipe->setPrice(mt_rand (0, 1) == 1 ? mt_rand(1, 1000) : null);
    $recipe->setIsPublic(mt_rand(0, 1) == 1 ? true : false);
    $recipe->setUser($users[mt_rand(0,count($users)-1)]);
    for ($k=0; $k<mt_rand(5, 15); $k++){
        $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients)-1)]);
        $manager->persist($recipe);
    }
    $recipes[]=$recipe;
    $manager->persist($recipe);
}

foreach($recipes as $recipe){
    for ($i=0;$i<mt_rand(0,4);$i++){
        $mark=new Mark();
        $mark->setMark(mt_rand(1,5))
            ->setUser($users[mt_rand(0,count($users)-1)])
            ->setRecipe($recipe);
        $manager->persist($mark);
    }
}

$manager->flush();



}
}
