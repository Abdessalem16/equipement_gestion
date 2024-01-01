<?php
namespace App\Controller;

// ob_start(); // Start output buffering
// session_start();


// ob_end_flush(); 


use App\Entity\Equipe;

use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    
    /**
     * @Route("/", name="equipe_list")
     */
    public function home(Request $request)
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        $equipements = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $propertySearch->getNom();   
            
            if ($nom != "") {
                $equipements = $this->getDoctrine()->getRepository(Equipe::class)->findBy(['nom' => $nom]);
            } else {
                $equipements = $this->getDoctrine()->getRepository(Equipe::class)->findAll();
            }
        }

        return $this->render('equipements/index.html.twig', ['form' => $form->createView(), 'equipements' => $equipements]);
    }








    /**
 * @Route("/equipe/new", name="new_equipe")
 * Method({"GET","POST"})
 */
public function new(Request $request)
{
    $equipe = new Equipe();
    $form = $this->createFormBuilder($equipe)
        ->add('nom', TextType::class)
        ->add('description', TextType::class)
        ->add('save', SubmitType::class, array('label' => 'créer'))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $equipe = $form->getData();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipe);
        $entityManager->flush();

        return $this->redirectToRoute('equipe_list');
    }

    return $this->render('equipements/new.html.twig', ['form' => $form->createView()]);
}

/**
 *  @Route("/equipe/{id}", name="equipe_show")
 */
public function show($id){
    $equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($id);
    return $this->render('equipements/show.html.twig',
    ['equipe'=> $equipe]);
}
/**
 * @Route("/equipe/edit/{id}", name="edit_equipe")
 * Method({"GET","POST"})
 */
public function edit(Request $request, $id)
{
    $equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($id);

    $form = $this->createFormBuilder($equipe)
        ->add('nom', TextType::class)
        ->add('description', TextType::class)
        ->add('save', SubmitType::class, array('label' => 'Modifier'))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('equipe_list');
    }

    return $this->render('equipements/edit.html.twig', ['form' => $form->createView()]);
}
/**
 * @Route("/equipe/delete/{id}", name="delete_equipe")
 * @Method({"DELETE"})
 */
public function delete(Request $request, $id)
{
    $equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($id);
    
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($equipe);
    $entityManager->flush();

    $response = new Response();
    $response->send();

    return $this->redirectToRoute('equipe_list');
}

//        /**
//  * @Route("/equipe/save")
//  */
// public function save()
// {
//     $entityManager = $this->getDoctrine()->getManager();
    
//     $equipe = new Equipe();
//     $equipe->setNom('equipe 3'); // Use the correct method: setNom
//     $equipe->setDescription('trois');

//     $entityManager->persist($equipe);
//     $entityManager->flush();

//     return new Response('Equipe enregistrée avec id '.$equipe->getId()); // Use getId to get the id
// }




}
