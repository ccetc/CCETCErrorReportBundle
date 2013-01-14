<?php
namespace CCETC\ErrorReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class CCETCErrorReportExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.xml');

        if (!isset($config['support_email'])) {
            throw new \InvalidArgumentException('The "support_email" option must be set');
        } else {
            $container->setParameter('ccetc_error_report.support_email', $config['support_email']);
        }
        $container->setParameter('ccetc_error_report.direct_email_subject', $config['direct_email_subject']);        
        $container->setParameter('ccetc_error_report.additional_template', $config['additional_template']);        
    }

}