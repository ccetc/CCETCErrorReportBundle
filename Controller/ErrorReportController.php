<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sonata\AdminBundle\Controller\CoreController;

class ErrorReportController extends CoreController
{

  public function errorReportAction()
  {
  	// TODO: get support email from config
//    $supportEmail = $this->get('gjgny')->supportEmail;
	$supportEmail = "haggertypat@gmail.com";

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

        $errorReport = new \CCETC\ErrorReportBundle\Entity\ErrorReport();
        $errorReport->setContent($data['content']);
        $errorReport->setWriterEmail($data['email']);
        $errorReport->setDatetimeReported(new \DateTime());

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($errorReport);
        $em->flush();

        $session->setFlash('adminMessage', 'Your report has been submitted.  Thank you');

		// TODO: return home
        return $this->render('CCETCErrorReportBundle:submitErrorReport.html.twig', array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail
        ));
      }
      else
      {
        return $this->render('CCETCErrorReportBundle:submitErrorReport.html.twig', array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail
        ));
      }
    }
    else
    {
      return $this->render('CCETCErrorReportBundle:submitErrorReport.html.twig', array(
          'errorReportForm' => $form->createView(),
          'supportEmail' => $supportEmail
      ));
    }
  }

}
