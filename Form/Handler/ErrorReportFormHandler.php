<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCETC\ErrorReportBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ErrorReportHandler
{

    protected $request;
    protected $form;

    public function __construct(Form $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    public function process($supportEmail, $fromEmail)
    {
        if('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if($this->form->isValid()) {
                $this->onSuccess($supportEmail, $fromEmail);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess($supportEmail, $fromEmail)
    {
        $session = $this->request->getSession();
        $data = $this->form->getData();

        $errorReport = new \CCETC\ErrorReportBundle\Entity\ErrorReport();
        $errorReport->setContent($data['content']);
        $errorReport->setDatetimeReported(new \DateTime());
        $errorReport->setOpen(true);
        $errorReport->setSpam(false);
        $errorReport->setRequestServer($data['request_server']);

        if(!$this->get('security.context')->isGranted('ROLE_USER')) {
            $errorReport->setWriterEmail($data['email']);
        }
        else {
            $user = $this->get('security.context')->getToken()->getUser();
            $errorReport->setUserSubmittedBy($user);
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($errorReport);
        $em->flush();

        $this->sendSupportEmail($fromEmail, $supportEmail, $errorRreport);
    }

    public function sendSupportEmail($fromEmail, $supportEmail, $errorRreport)
    {
        if($errorReport->getUserSubmittedBy()) {
            $who = 'A user named "' . $errorReport->getUserSubmittedBy()->__toString() . '" (email: <a href="' . $errorReport->getUserSubmittedBy()->getEmail() . '">' . $errorReport->getUserSubmittedBy()->getEmail() . '</a>)';            
        } else {
            $who = 'An anonymous user';
            if($errorReport->getEmail()) {
                $who .= ' (email: <a href="' . $errorReport->getEmail() . '">' . $errorReport->getEmail() . '</a>)';
            }
        }
        
        $message = \Swift_Message::newInstance()
                ->setSubject('Error Report Submitted')
                ->setFrom($fromEmail)
                ->setTo($supportEmail)
                ->setContentType('text/html')
                ->setBody('<html>
                       ' . $who . ' submitted this error report on ' . $errorReport->getDatetimeReported()->format('Y-m-d H:i:s') . '.<br/><br/>
                        Content: <br/>' . $errorReport->getContent() . '</html>')

        ;
        $this->get('mailer')->send($message);
    }

}
