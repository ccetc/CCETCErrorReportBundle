<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ErrorReportController extends Controller
{

    public function errorReportAction($includeBreadcrumb = false, $flash = 'message', $redirect = 'home', $baseLayout, $formRoute = 'help')
    {
        $supportEmail = $this->get('errorReports')->supportEmail;
        
        $form = $this->createFormBuilder();
        
        if(!$this->get('security.context')->isGranted('ROLE_USER'))
        {
            $form->add('email', 'text', array(
                'label' => 'Enter your e-mail address if you would like to be contacted about this error: ',
                'required' => false
            ));
        }
        
        $form->add('content', 'textarea', array('label' => 'Please enter a description of this error.'));

        
        $form = $form->getForm();
        
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

                $session->setFlash($flash, 'Your report has been submitted.  Thank you');

                return $this->redirect($this->generateUrl($redirect));
            }
            else
            {
                return $this->render('CCETCErrorReportBundle:ErrorReport:submit.html.twig', array(
                    'errorReportForm' => $form->createView(),
                    'supportEmail' => $supportEmail,
                    'base_layout' => $baseLayout,
                    'includeBreadcrumb' => $includeBreadcrumb,
                    'formRoute' => $formRoute
                ));
            }
        }
        else
        {
            return $this->render('CCETCErrorReportBundle:ErrorReport:submit.html.twig', array(
                'errorReportForm' => $form->createView(),
                'supportEmail' => $supportEmail,
                'base_layout' => $baseLayout,
                'includeBreadcrumb' => $includeBreadcrumb,
                'formRoute' => $formRoute
            ));
        }
    }

}
