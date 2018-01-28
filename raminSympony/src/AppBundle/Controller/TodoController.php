<?php

namespace AppBundle\Controller;

use AppBundle\Entity\todo;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;





class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $produits= $this->getDoctrine()->getRepository('AppBundle:todo')->findAll();
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig',array('produits'=>$produits));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {

        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository('AppBundle:todo')->find($id);
        $em->remove($produit);
        $em->flush();
        $this->addFlash('Notice','Produit a Suprimee');
        return $this->redirectToRoute('todo_list');

    }

        /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $produit=new todo;
        $form=$this->createFormBuilder($produit)
            ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('prix',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('souvgarder',SubmitType::class,array('label'=>'Envoyer','attr'=>array('class'=>'btn btn-success','style'=>'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
           // die('SUBMITTED');

            //prendre data a partir de Formulaire
            $nom=$form['nom']->getData();
            $description=$form['description']->getData();
            $prix=$form['prix']->getData();

            //passer a Bd les datas
            $produit->setNom($nom);
            $produit->setDescription($description);
            $produit->setPrix($prix);

            $em=$this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            $this->addFlash('Notice','Produit a Ajoutee');


            //Envoyer un courriel 
            //===================================
            $message = \Swift_Message::newInstance()
                ->setFrom('autopartage123@gmail.com')
                ->setTo('ramin.jorboze@gmail.com')
                ->setBody($this->renderView('todo/registration.html.twig'),'text/html');
            $this->get('mailer')->send($message);

            //===================================
            return $this->redirectToRoute('todo_list');
        }


        // replace this example code with whatever you need
        return $this->render('todo/create.html.twig',array('form'=>$form->createView()));
    }

        /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id,Request $request)
    {

        $produit= $this->getDoctrine()->getRepository('AppBundle:todo')->find($id);
        $produit->setNom($produit->getNom());
        $produit->setDescription($produit->getDescription());
        $produit->setPrix($produit->getPrix());


        $form=$this->createFormBuilder($produit)
            ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('prix',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
            ->add('souvgarder',SubmitType::class,array('label'=>'Envoyer','attr'=>array('class'=>'btn btn-success','style'=>'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
           // die('SUBMITTED');

            //prendre data a partir de Formulaire
            $nom=$form['nom']->getData();
            $description=$form['description']->getData();
            $prix=$form['prix']->getData();

            $em=$this->getDoctrine()->getManager();
            $produit=$em->getRepository('AppBundle:todo')->find($id);

            //passer a Bd les datas
            $produit->setNom($nom);
            $produit->setDescription($description);
            $produit->setPrix($prix);

            
            $em->flush();

            $this->addFlash('Notice','Produit a Modifiee');

            return $this->redirectToRoute('todo_list');
        }



        // replace this example code with whatever you need
        return $this->render('todo/edit.html.twig',array('produit'=>$produit,'form'=>$form->createView()));
    }

        /**
     * @Route("/todo/details/{id}", name="todo_detailes")
     */
    public function detailesAction($id)
    {

       $produit= $this->getDoctrine()->getRepository('AppBundle:todo')->find($id);
        // replace this example code with whatever you need
        return $this->render('todo/details.html.twig',array('produit'=>$produit));
    }


}
