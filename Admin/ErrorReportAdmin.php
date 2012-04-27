<?php

namespace CCETC\ErrorReportBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ErrorReportAdmin extends Admin
{
    protected $maxPerPage = 20;
    
    protected $entityIconPath = 'bundles/sonataadmin/famfamfam/bullet_error.png';

    protected $entityLabelPlural = "Error Reports";
    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('userSubmittedBy', 'string', array('label' => 'Submitted By', 'template' => 'CCETCErrorReportBundle:ErrorReport:_list_field_submitted_by.html.twig'))
                ->add('datetimeReported')
                ->add('content')		
                ->add('spam')
                ->add('open')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'view' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    ),
                    'label' => 'Actions'
                ))
        ;
    }
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['open'] = array(
            'label' => 'Open Selected',
            'ask_confirmation' => false
        );

        $actions['close'] = array(
            'label' => 'Close Selected',
            'ask_confirmation' => false
        );
        
        $actions['spam'] = array(
            'label' => 'Mark Selected as Spam',
            'ask_confirmation' => false
        );

        $actions['unspam'] = array(
            'label' => 'Mark Selected as Not Spam',
            'ask_confirmation' => false
        );
        
        return $actions;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('spam')
            ->add('open')
        ;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('writerEmail', null, array('required' => false))
                ->add('userSubmittedBy', null, array('label' => 'User', 'required' => false))
                ->add('datetimeReported', null, array('label' => 'Datetime Reported', 'required' => false, 'date_format' => 'MM/dd/yyyy'))
                ->add('content', null, array('required' => false))
                ->add('spam', null, array('required' => false))
                ->add('open', null, array('required' => false))
		;
    }
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
                ->add('writerEmail')
                ->add('userSubmittedBy', null, array('label' => 'User'))
                ->add('datetimeReported')
                ->add('content')
                ->add('spam')
                ->add('open')
                ->add('requestServer', null, array('template' => 'CCETCErrorReportBundle:ErrorReport:_show_request_server.html.twig'))
		;
    }

}
