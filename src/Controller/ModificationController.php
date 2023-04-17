<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ModificationController extends AbstractController
{
#[Security("is_granted('ROLE_USER') and user === the_user")]
#[Route('/modification/{id}', name: 'app_modification', methods:['GET','POST'])]
public function modification(User $the_user, Request $request, EntityManagerInterface $manager): Response
{
$form=$this->createForm(UserType::class,$the_user);
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
    $the_user=$form->getData();
    $manager->persist($the_user);
    $manager->flush();
    $this->addFlash('success','Les informations ont bien été modifiées.');
    return $this->redirectToRoute('app_recipe');
}
return $this->render('compte utilisateur/modification.html.twig',['form'=>$form->createView()]);
}
}
