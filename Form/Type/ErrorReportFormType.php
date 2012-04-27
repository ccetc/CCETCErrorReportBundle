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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

class ErrorReportFormType extends AbstractType
{
    protected $isLoggedIn;
    protected $request;
    
    public function __construct($isLoggedIn, $request)
    {
        $this->isLoggedIn = $isLoggedIn;
        $this->request = $request;
    }
    
    public function buildForm(FormBuilder $builder, array $options = array())
    {
        if(!$this->isLoggedIn) {
            $form->add('email', 'text', array(
                'label' => 'Enter your e-mail address if you would like to be contacted about this error: ',
                'required' => false
            ));
        }

        $builder->add('content', 'textarea', array('label' => 'Please enter a description of this error.'));
        $builder->add('request_server', 'hidden');
        
        $builder->
            addValidator(new CallbackValidator(function(FormInterface $form)
            {
                if (!$form["content"]->getData())
                {
                    $builder->addError(new FormError('Please enter a description of this error.'));
                }
            })
        );
        
        $request_server = print_r($this->request->server, true);    
        $builder->setData(array('request_server' => $request_server));
    }
    
    public function getName()
    {
        return 'ccetc_error_report';
    }
}
