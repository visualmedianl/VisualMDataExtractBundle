<?php
/**
 * Bundle Definition
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VisualM\DataExtractBundle\DependencyInjection\Compiler\AddDataProvidersCompilerPass;

/**
 * Bundle Definition
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class VisualMDataExtractBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Add CompilerPass that scans for tagged services that provide
        // an DataProviderInterface
        $container->addCompilerPass(new AddDataProvidersCompilerPass());

    }

}
