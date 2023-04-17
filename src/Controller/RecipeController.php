<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\PublishType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RecipeController extends AbstractController
{
#[IsGranted('ROLE_USER')]
#[Route('/recette', name: 'app_recipe', methods:['GET'])]
public function index(RecipeRepository $repository,PaginatorInterface $paginator, Request $request): Response
{

    $recipes=$paginator->paginate(
        $repository->findBy(['user'=>$this->getUser()]),
        $request->query->getInt('page',1/* Nombre de page */),10/* Page max */);
        return $this->render('recette/mes_recettes.html.twig',['recipes'=>$recipes]);
}

#[IsGranted('ROLE_USER')]
#[Route('/recette/publication/{id}/{isPublic}', name: 'app_publication')]
public function publier(Recipe $recipe, bool $isPublic, EntityManagerInterface $entityManager): Response
{
    $recipe->setIsPublic($isPublic);
    $entityManager->persist($recipe);
    $entityManager->flush();

    return $this->redirectToRoute('app_recipe');
}

#[IsGranted('ROLE_USER')]
#[Route('/recette/creation', name: 'recette.new', methods:['GET','POST'])]
public function new(Request $request, EntityManagerInterface $manager): Response
{
    $recipe= new Recipe();
    $form=$this->createForm(RecipeType::class,$recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
        $recipe = $form->getData();
        $recipe->setUser($this->getUser());
        $manager->persist($recipe);
        $manager->flush();
        $this->addFlash('success','Votre recette a été créé.');
        return $this->redirectToRoute('app_recipe');
    }
    return $this->render('recette/nouvelle_recette.html.twig',['form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
#[Route('/recette/modification/{id}', name: 'recette.edit', methods:['GET','POST'])]
public function edit(Recipe $recipe,Request $request, EntityManagerInterface $manager):Response 
{
    $form=$this->createForm(RecipeType::class,$recipe);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        $recipe=$form->getData();
        $manager->persist($recipe);
        $manager->flush();
        $this->addFlash('success','Votre recette a été modifié.');
        return $this->redirectToRoute('app_recipe');
}
    return $this->render('recette/edit.html.twig',['form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
#[Route('/recette/suppression/{id}', name:'recette.delete', methods:['GET'])]
public function delete(EntityManagerInterface $manager, Recipe $recipe):Response
{
    $manager->remove($recipe);
    $manager->flush();
    $this->addFlash('success','Votre ingrédient a été supprimé .');
    return $this->redirectToRoute('app_recipe');
}

#[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true")]
#[Route('/note/{id}',name:'app_note',methods:['GET','POST'])]
public function publication(Request $request,MarkRepository $markRepository,Recipe $recipe,EntityManagerInterface $manager)
{
    $mark=new Mark();
    $form=$this->createForm(MarkType::class,$mark);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        $mark->setUser($this->getUser());
        $mark->setRecipe($recipe);
        $existingMark = $markRepository->findOneBy(['user'=>$this->getUser(),'recipe'=>$recipe]);
        if(!$existingMark){
            $manager->persist($mark);
        }else{
            $existingMark->setMark($form->getData()->getMark());
        }
        $manager->flush();
        $this->addFlash('success','Votre note à bien été prise en compte.');
        return $this->redirectToRoute('app.publique');
    }
    return $this->render('recette/public.html.twig',['recipe'=>$recipe,'form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER')")]
#[Route('/publier/{id}',name:'app_publication',methods:['GET','POST'])]
public function publications(Recipe $recipe,Request $request, EntityManagerInterface $manager)
{
    $form=$this->createForm(PublishType::class,$recipe);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        $recipe->setIsPublic(true);
        $recipe=$form->getData();
        $manager->persist($recipe);
        $manager->flush();
        $this->addFlash('success','Votre recette a été publié.');
        return $this->redirectToRoute('app_recipe');
}
    return $this->render('recette/publier.html.twig',['recipe'=>$recipe,'form'=>$form->createView()]);
}

#[Security("is_granted('ROLE_USER')")]
#[Route('/depublier/{id}',name:'app_depublication',methods:['GET','POST'])]
public function depublications(Recipe $recipe, EntityManagerInterface $manager)
{

    if ($recipe->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à effectuer cette action.');
    }
    $recipe->setIsPublic(false);
    $manager->flush();
    $this->addFlash('success','Votre recette n\'est plus publiée.');
    return $this->redirectToRoute('app_recipe', ['id' => $recipe->getId()]);
}

#[Route('/recettes/communaute', name:'app.publique',methods:['GET'])]
public function publique(PaginatorInterface $paginator,RecipeRepository $repository, Request $request ):Response{
    $recipes=$paginator->paginate($repository->findPublicRecipe(null),$request->query->getInt('page',1),10);
    return $this->render('recette/publique.html.twig',['recipes'=>$recipes]);
}
}
