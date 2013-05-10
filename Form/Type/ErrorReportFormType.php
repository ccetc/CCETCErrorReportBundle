<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCETC\ErrorReportBundle\Form\Type;

use CCETC\ErrorReportBundle\Entity\ErrorReport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

class ErrorReportFormType extends AbstractType
{
    protected $currentUserIsLoggedIn;
    protected $request;
    
    public function __construct($currentUserIsLoggedIn, $request)
    {
        $this->currentUserIsLoggedIn = $currentUserIsLoggedIn;
        $this->request = $request;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', array('label' => 'Please enter a description of this error.'));
        $builder->add('request_server', 'hidden');

        if(!$this->currentUserIsLoggedIn) {
            $builder->add('writerEmail', 'text', array(
                'label' => 'Enter your e-mail address if you would like to be contacted about this error: ',
                'required' => false
            ));
        }
        
        $requestServer = print_r($this->request->server, true);
        $errorReport = new ErrorReport;
        $errorReport->setRequestServer($requestServer);
        $builder->setData($errorReport);
    }
    
    public function getName()
    {
        return 'ccetc_error_report';
    }
}