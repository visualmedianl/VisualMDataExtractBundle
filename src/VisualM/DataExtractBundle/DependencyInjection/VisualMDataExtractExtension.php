<?php
/**
 * Dependency Injection for VisualMDataExtractBundle
 *
 * @author Elze Kool <info@visualmedia.nl>
 */

namespace VisualM\DataExtractBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Dependency Injection
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class VisualMDataExtractExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

}
