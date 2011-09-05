<?php

namespace GJGNY\DataToolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sonata\AdminBundle\Controller\CoreController;

class HelpController extends CoreController
{

  public function helpAction()
  {
    $supportEmail = $this->get('gjgny')->supportEmail;

    $form = $this->createFormBuilder()
            ->add('email', 'text', array(
                'label' => 'Enter your e-mail address if you would like to be contacted about this error.',
                'required' => false
            ))
            ->add('content', 'textarea', array('label' => 'Please enter a description of the problem that you are experiencing.'))
            ->getForm();

    $request = $this->get('request');
    
    if($request->getMethod() == 'POST')
    {
      $form->bindRequest($request);

      if($form->isValid())
      {
        $session = $this->getRequest()->getSession();
        $data = $form->getData();

        $errorReport = new \GJGNY\DataToolBundle\Entity\ErrorReport();
        $errorReport->setContent($data['content']);
        $errorReport->setWriterEmail($data['email']);
        $errorReport->setDatetimeReported(new \DateTime());

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($errorReport);
        $em->flush();

        $session->setFlash('adminMessage', 'Your report has been submitted.  Thank you');

        return $this->render('SonataAdminBundle:Core:dashboard.html.twig', array(
            'groups' => $this->get('sonata.admin.pool')->getDashboardGroups(),
            'base_template'  => $this->getBaseTemplate(),
        ));
      }
      else
      {
        return $this->render('GJGNYDataToolBundle:Help:help.html.twig', array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail
        ));
      }
    }
    else
    {
      return $this->render('GJGNYDataToolBundle:Help:help.html.twig', array(
          'errorReportForm' => $form->createView(),
          'supportEmail' => $supportEmail
      ));
    }
  }

}
