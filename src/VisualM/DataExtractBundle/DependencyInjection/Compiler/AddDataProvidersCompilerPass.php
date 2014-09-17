<?php
/**
 * Add DataProviders Compiler Pass
 *
 * @author Elze Kool <info@visualmedia.nl>
 */

namespace VisualM\DataExtractBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add DataProviders Compiler Pass
 *
 * Scans the Container for services tagged with visualm.data.data_provider
 * and adds them to the Dictionary Collector
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class AddDataProvidersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('visualm.data.dictionary_collector')) {
            return;
        }

        $collector = $container->getDefinition('visualm.data.dictionary_collector');

        $tagged_services = $container->findTaggedServiceIds(
            'visualm.data.data_provider'
        );

        // Find services tagged
        foreach ($tagged_services as $id => $tag_attributes) {
            // Go trough tags
            foreach ($tag_attributes as $attributes) {
                $collector->addMethodCall(
                    'addProvider',
                    array(new Reference($id), isset($attributes['cachable']) ? $attributes['cachable'] : true)
                );
            }

        }

    }
}
