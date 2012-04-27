<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use CCETC\ErrorReportBundle\Form\Type\ErrorReportFormType;
use CCETC\ErrorReportBundle\Form\Handler\ErrorReportFormHandler;
use CCETC\ErrorReportBundle\Entity\ErrorReport;

class ErrorReportController extends Controller
{

    public function helpPageAction($usePageHeader = false, $flash = 'alert-message', $redirect = 'home', $baseLayout, $formRoute = 'help')
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $currentUserIsLoggedIn = $this->get('security.context')->isGranted('ROLE_USER');
        
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');

        $errorReport = new ErrorReport();
        $form = $this->createForm(new ErrorReportFormType($currentUserIsLoggedIn, $request), $errorReport);
        $handler = new ErrorReportFormHandler($form, $request, $this->getDoctrine(), $this->get('mailer'), $supportEmail, $fromEmail, $currentUser, $currentUserIsLoggedIn);

        if( $handler->formIsValid() ) {
            $handler->process();
            $session->setFlash($flash, 'Your report has been submitted.  Thank you');
            return $this->redirect($this->generateUrl($redirect));         
        } else {
            $templateParameters = array(
                'base_layout' => $baseLayout,
                'usePageHeader' => $usePageHeader,
                'formRoute' => $formRoute,
                'flash' => $flash,
                'redirect' => $redirect,
                'form' => $form,
                'handler' => $handler
            );

            if(class_exists('Sonata\AdminBundle\SonataAdminBundle')) {
                $adminPool = $this->container->get('sonata.admin.pool');
                $templateParameters['admin_pool'] = $adminPool;
            }


            return $this->render('CCETCErrorReportBundle:ErrorReport:help_page.html.twig', $templateParameters);
        }
    }

    public function errorReportFormAction($formRoute = 'help', $formText = 'Having trouble with something?', $form = null, $handler = null)
    {
        $request = $this->get('request');
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        $directEmailSubject = $this->container->getParameter('ccetc_error_report.direct_email_subject');
        
        if(!$form) {            
            $currentUserIsLoggedIn = $this->get('security.context')->isGranted('ROLE_USER');
            $errorReport = new ErrorReport();
            $form = $this->createForm(new ErrorReportFormType($currentUserIsLoggedIn, $request), $errorReport);
        } 

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
