<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CCETC\ErrorReportBundle\Form\Type\ErrorReportFormType;
use CCETC\ErrorReportBundle\Form\Handler\ErrorReportHandler;

class ErrorReportController extends Controller
{

    public function helpPageAction($usePageHeader = false, $flash = 'alert-message', $redirect = 'home', $baseLayout, $formRoute = 'help')
    {
        $request = $this->getRequest();
        $isLoggedIn = $this->get('security.context')->isGranted('ROLE_USER');    
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');

        $form = $this->createForm(new ErrorReportFormType($isLoggedIn, $request));
        $handler = new ErrorReportHandler();

        if( $handler->process($form, $request, $flash, $redirect) ) {
            $session->setFlash($flash, 'Your report has been submitted.  Thank you');
            
            return $this->redirect($this->generateUrl($redirect));         
        } else {
            $templateParameters = array(
                'base_layout' => $baseLayout,
                'usePageHeader' => $usePageHeader,
                'formRoute' => $formRoute,
                'flash' => $flash,
                'redirect' => $redirect,
            );

            if(class_exists('Sonata\AdminBundle\SonataAdminBundle')) {
                $adminPool = $this->container->get('sonata.admin.pool');
                $templateParameters['admin_pool'] = $adminPool;
            }


            return $this->render('CCETCErrorReportBundle:ErrorReport:help_page.html.twig', $templateParameters);
        }
    }

    public function errorReportFormAction($formRoute = 'help', $formText = 'Having trouble with something?')
    {
        $request = $this->get('request');
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        $directEmailSubject = $this->container->getParameter('ccetc_error_report.direct_email_subject');
        $isLoggedIn = $this->get('security.context')->isGranted('ROLE_USER');        

        $form = $this->createForm(new ErrorReportFormType($isLoggedIn, $request));
        
        $templateParameters = array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail,
            'formRoute' => $formRoute,
            'directEmailSubject' => $directEmailSubject,
            'formText' => $formText,
        );

        return $this->render('CCETCErrorReportBundle:ErrorReport:_error_report_form.html.twig', $templateParameters);
    }
}
