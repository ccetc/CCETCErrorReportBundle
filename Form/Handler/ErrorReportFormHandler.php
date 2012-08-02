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

class ErrorReportFormHandler
{

    protected $request;
    protected $form;
    protected $mailer;
    protected $supportEmail;
    protected $fromEmail;
    protected $currentUser;
    protected $currentUserIsLoggedIn;
    protected $entityManager;
    
    public function __construct(Form $form, Request $request, $mailer, $supportEmail, $fromEmail, $currentUser, $currentUserIsLoggedIn, $entityManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->mailer = $mailer;
        $this->supportEmail = $supportEmail;
        $this->fromEmail = $fromEmail;
        $this->currentUser = $currentUser;
        $this->currentUserIsLoggedIn = $currentUserIsLoggedIn;
        $this->entityManager = $entityManager;
    }
    
    public function process()
    {
        if('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if($this->form->isValid()) {
                $this->onSuccess();
                return true;
            }
        }
        return false;
    }

    protected function onSuccess()
    {
        $session = $this->request->getSession();
        $errorReport = $this->form->getData();

        $errorReport->setDatetimeReported(new \DateTime());
        $errorReport->setOpen(true);
        $errorReport->setSpam(false);

        if($this->currentUserIsLoggedIn) {
            $errorReport->setUserSubmittedBy($this->currentUser);
        }

        $this->entityManager->persist($errorReport);
        $this->entityManager->flush();
        
        $this->sendSupportEmail($errorReport);
    }

    public function sendSupportEmail($errorReport)
    {
        if($errorReport->getUserSubmittedBy()) {
            $who = 'A user named "' . $errorReport->getUserSubmittedBy()->__toString() . '" (email: <a href="' . $errorReport->getUserSubmittedBy()->getEmail() . '">' . $errorReport->getUserSubmittedBy()->getEmail() . '</a>)';            
        } else {
            $who = 'An anonymous user';
            if($errorReport->getWriterEmail()) {
                $who .= ' (email: <a href="' . $errorReport->getWriterEmail() . '">' . $errorReport->getWriterEmail() . '</a>)';
            }
        }
        
        $message = \Swift_Message::newInstance()
                ->setSubject('Error Report Submitted')
                ->setFrom($this->fromEmail)
                ->setTo($this->supportEmail)
                ->setContentType('text/html')
                ->setBody('<html>
                       ' . $who . ' submitted this error report on ' . $errorReport->getDatetimeReported()->format('Y-m-d H:i:s') . '.<br/><br/>
                        Content: <br/>' . $errorReport->getContent() . '</html>')

        ;
        $this->mailer->send($message);
    }

}
