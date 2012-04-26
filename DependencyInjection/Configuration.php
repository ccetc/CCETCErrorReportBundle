<?php
namespace CCETC\ErrorReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccetc_error_report');

        $rootNode
            ->children()
                ->scalarNode('support_email')->cannotBeEmpty()->end()
            ->end()
            ->children()
                ->scalarNode('direct_email_subject')->defaultvalue("Error Report")->end()
            ->end()                
        ;

        return $treeBuilder;
    }
}