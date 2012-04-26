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
                ->add('writerEmail')
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
                ->add('writerEmail')
                ->add('datetimeReported')
                ->add('content')
                ->add('spam')
                ->add('open')
		;
    }
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
                ->add('writerEmail')
                ->add('datetimeReported')
                ->add('content')
                ->add('spam')
                ->add('open')
		;
    }

}
