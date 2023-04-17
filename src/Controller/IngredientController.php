<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IngredientController extends AbstractController
{

#[IsGranted('ROLE_USER')]
#[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
public function index(IngredientRepository $repository,PaginatorInterface $paginator, Request $request): Response
{
$ingredients=$paginator->paginate(
    $repository->findBy(['user'=>$this->getUser()]),
    $request->query->getInt('page',1/* Nombre de page */),10/* Page max */);
    return $this->render('ingredient/mes_ingredients.html.twig',['ingredients'=>$ingredients]);
}

#[IsGranted('ROLE_USER')]
#[Route('/ingredient/creation',name: 'ingredient.new', methods:['GET','POST'])]
public function new(Request $request, EntityManagerInterface $manager): Response
{
$ingredient= new Ingredient();
$form=$this->createForm(IngredientType::class,$ingredient);
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
    $ingredient=$form->getData();
    $ingredient->setUser($this->getUser());
    $manager->persist($ingredient);
    $manager->flush();
    $this->addFlash('success','Votre ingrédient a été créé.');
    return $this->redirectToRoute('app_ingredient');
}
return $this->render('ingredient/nouveau_ingredient.html.twig',['form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
#[Route('/ingredient/modification/{id}', name: 'ingredient.edit', methods:['GET','POST'])]
public function edit(Ingredient $ingredient,Request $request, EntityManagerInterface $manager):Response 
{
$form=$this->createForm(IngredientType::class,$ingredient);
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
    $ingredient=$form->getData();
    $manager->persist($ingredient);
    $manager->flush();
    $this->addFlash('success','Votre ingrédient a été modifié.');
    return $this->redirectToRoute('app_ingredient');
}
return $this->render('ingredient/edit.html.twig',['form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
#[IsGranted('ROLE_USER')]
#[Route('/ingredient/suppression/{id}', name:'ingredient.delete', methods:['GET'])]
public function delete(EntityManagerInterface $manager, Ingredient $ingredient):Response
{
$manager->remove($ingredient);
$manager->flush();
$this->addFlash('success','Votre ingrédient a été supprimé .');
return $this->redirectToRoute('app_ingredient');
}

}