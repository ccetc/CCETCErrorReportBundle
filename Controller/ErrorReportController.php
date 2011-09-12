<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Controller\CoreController;

class ErrorReportController extends CoreController
{

    public function errorReportAction($headingBlock = null, $flash = 'message', $redirect = 'home', $baseLayout)
    {
        // TODO: get support email from config
//    $supportEmail = $this->get('gjgny')->supportEmail;
        $supportEmail = "haggertypat@gmail.com";

        $form = $this->createFormBuilder()
                ->add('email', 'text', array(
                    'label' => 'Enter your e-mail address if you would like to be contacted about this error: ',
                    'required' => false
                ))
                ->add('content', 'textarea', array('label' => 'Please enter a description of the problem that you have.'))
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

                $session->setFlash($flash, 'Your report has been submitted.  Thank you');

                return $this->redirect($this->generateUrl($redirect));
            }
            else
            {
                return $this->render('CCETCErrorReportBundle:ErrorReport:submit.html.twig', array(
                    'errorReportForm' => $form->createView(),
                    'supportEmail' => $supportEmail,
                    'base_layout' => $baseLayout,
                    'heading_block' => $headingBlock,
                ));
            }
        }
        else
        {
            return $this->render('CCETCErrorReportBundle:ErrorReport:submit.html.twig', array(
                'errorReportForm' => $form->createView(),
                'supportEmail' => $supportEmail,
                'base_layout' => $baseLayout,
                'heading_block' => $headingBlock,
            ));
        }
    }

}
