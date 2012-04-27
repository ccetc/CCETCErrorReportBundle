<?php

namespace CCETC\ErrorReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

class ErrorReportController extends Controller
{

    public function helpPageAction($usePageHeader = false, $flash = 'alert-message', $redirect = 'home', $baseLayout, $formRoute = 'help')
    {
        $request = $this->getRequest();

        // if the form is validate, process and direct for generating any output
        if($request->getMethod() == 'POST') {
            $form = $this->buildErrorReportForm();
            $form->bindRequest($request);
            if($form->isValid()) {
                return $this->handleErrorReportForm($form, $request, $flash, $redirect);
            }
        }

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

    public function errorReportFormAction($usePageHeader = false, $flash = 'alert-message', $redirect = 'home', $formRoute = 'help', $formText = 'Having trouble with something?')
    {
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');
        $directEmailSubject = $this->container->getParameter('ccetc_error_report.direct_email_subject');

        $form = $this->buildErrorReportForm();

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $this->handleErrorReportForm($form, $request, $flash, $redirect);
        }

        $templateParameters = array(
            'errorReportForm' => $form->createView(),
            'supportEmail' => $supportEmail,
            'usePageHeader' => $usePageHeader,
            'formRoute' => $formRoute,
            'directEmailSubject' => $directEmailSubject,
            'formText' => $formText,
        );

        return $this->render('CCETCErrorReportBundle:ErrorReport:_error_report_form.html.twig', $templateParameters);
    }

    public function handleErrorReportForm($form, $request, $flash, $redirect)
    {
        $supportEmail = $this->container->getParameter('ccetc_error_report.support_email');

        $form->bindRequest($request);

        if($form->isValid()) {
            $session = $this->getRequest()->getSession();
            $data = $form->getData();

            $errorReport = new \CCETC\ErrorReportBundle\Entity\ErrorReport();
            $errorReport->setContent($data['content']);
            $errorReport->setDatetimeReported(new \DateTime());
            $errorReport->setOpen(true);
            $errorReport->setSpam(false);

            if(!$this->get('security.context')->isGranted('ROLE_USER')) {
                $errorReport->setWriterEmail($data['email']);
                $who = 'An anonymous user';
                if($data['email'])
                    $who .= ' (email: <a href="' . $data['email'] . '">' . $data['email'] . '</a>)';
            }
            else {
                $user = $this->get('security.context')->getToken()->getUser();
                $errorReport->setUserSubmittedBy($user);
                $who = 'A user named "' . $user->__toString() . '" (email: <a href="' . $user->getEmail() . '">' . $user->getEmail() . '</a>)';
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
                       ' . $who . ' submitted this error report on ' . $errorReport->getDatetimeReported()->format('Y-m-d H:i:s') . '.<br/><br/>
                        Content: <br/>' . $errorReport->getContent() . '</html>')

            ;
            $this->get('mailer')->send($message);

            $session->setFlash($flash, 'Your report has been submitted.  Thank you');

            if(!$this->get('security.context')->isGranted('ROLE_USER')) {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            } else {
                return $this->redirect($this->generateUrl($redirect));
            }
        }
    }

    public function buildErrorReportForm()
    {
        $form = $this->createFormBuilder();

        if(!$this->get('security.context')->isGranted('ROLE_USER')) {
            $form->add('email', 'text', array(
                'label' => 'Enter your e-mail address if you would like to be contacted about this error: ',
                'required' => false
            ));
        }

        $form->add('content', 'textarea', array('label' => 'Please enter a description of this error.'));

        $form->
            addValidator(new CallbackValidator(function(FormInterface $form)
            {
                if (!$form["content"]->getData())
                {
                    $form->addError(new FormError('Please enter a description of this error.'));
                }
            })
        );
        
        
        return $form->getForm();
    }

}
