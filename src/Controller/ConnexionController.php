<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnexionController extends AbstractController
{
#[Route('/connexion', name: 'app_connexion', methods:['GET','POST'])]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    $this->redirectToRoute('app_recipe');
return $this->render('compte utilisateur/connexion.html.twig', ['last_username' => $authenticationUtils->getLastUsername(),'error'=>$authenticationUtils->getLastAuthenticationError()]);

}

#[Route('/deconnexion',name: 'app_deconnexion')]
public function logout()
{

}

#[Route('/inscription', name: 'app_inscription', methods:['GET','POST'])]
public function registration(Request $request, EntityManagerInterface $manager):Response
{
$user=new User();
$form=$this->createForm(RegistrationType::class,$user);
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
    $user=$form->getData();
    $this->addFlash('success','Votre compte a bien été créé.');
    $manager->persist($user);
    $manager->flush();
    return $this->redirectToRoute('app_connexion');
}
return $this->render('compte utilisateur/inscription.html.twig',['form'=>$form->createView()]);
}

}
