<?php
/**
 * Dictionary Collector Interface
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use VisualM\DataExtractBundle\Provider\DataProviderInterface;

/**
 * Dictionary Collector Interface
 *
 * A Dictionary collector scans Doctrine Entities for the DataElement annotation
 * and builds a dictonary of available fields

 * @author Elze Kool <info@visualmedia.nl>
 */
interface DictionaryCollectorInterface
{

    /**
     * Add Data Provider
     *
     * Allows adding providers that allow providing data that cannot
     * be provided with annotations.
     *
     * @see \VisualM\DataExtractBundle\Provider\DataProviderInterface
     *
     * Cachable sets if the Provided fields list are cachable, this prevents
     * accessing the provider if it not needed, which increases speed if
     * lazy services are used.
     *
     * @param DataProviderInterface $provider Provider
     * @param boolean               $cachable Provided fields are cachable
     *
     * @return void
     */
    public function addProvider(DataProviderInterface $provider, $cachable = true);

    /**
     * Get registrated Providers
     *
     * Returns the list of Providers that where added trough addProvider()
     *
     * @return array Providers
     */
    public function getProviders();

    /**
     * Get Provided Fields for Doctrine Entities
     *
     * @return array Field configurations
     */
    public function getDoctrineProvidedFields();

    /**
     * Get Available fields in Dictionary
     *
     * @param array $types Filter types based on type
     *
     * @return array Available fields in dictionary
     */
    public function getAvailableFields(array $types = null);
}
