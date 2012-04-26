<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ErrorReportController extends Controller
{

    public function errorReportAction($usePageHeader = false, $flash = 'alert-message', $redirect = 'home', $baseLayout, $formRoute = 'help')
    {
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        
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
                $errorReport->setDatetimeReported(new \DateTime());

                if(!$this->get('security.context')->isGranted('ROLE_USER'))
                {
                    $errorReport->setWriterEmail($data['email']);
                }
                else
                {
                    $errorReport->setWriterEmail($this->get('security.context')->getToken()->getUser()->getEmail());
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($errorReport);
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('Error Report Submitted')
                        ->setFrom($this->container->getParameter('fos_user.registration.confirmation.from_email'))
                        ->setTo($supportEmail)
                        ->setContentType('text/html')
                        ->setBody('<html>
                           '.$errorReport->getWriterEmail().' submitted an error report on '.$errorReport->getDatetimeReported()->format('Y-m-d H:i:s').':<br/>
                            '.$errorReport->getContent().'</html>')
                           
                ;
                $this->get('mailer')->send($message);
		
                $session->setFlash($flash, 'Your report has been submitted.  Thank you');

                if(!$this->get('security.context')->isGranted('ROLE_USER'))
                {                
                    return $this->redirect($this->generateUrl('fos_user_security_login'));
                }
                else
                {
                    return $this->redirect($this->generateUrl($redirect));                    
                }
            }
        }
        
        $templateParameters = array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail,
            'base_layout' => $baseLayout,
            'usePageHeader' => $usePageHeader,
            'formRoute' => $formRoute
        );
        
        if(class_exists('Sonata\AdminBundle\SonataAdminBundle')) {
            $adminPool = $this->container->get('sonata.admin.pool');
            $templateParameters['admin_pool'] = $adminPool;
        }

        return $this->render('CCETCErrorReportBundle:ErrorReport:submit.html.twig', $templateParameters);
    }

}
