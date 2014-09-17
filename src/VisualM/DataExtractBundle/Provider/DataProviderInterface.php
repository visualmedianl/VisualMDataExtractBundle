<?php
/**
 * DataProvider Interface
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Provider;

/**
 * DataProvider Interface
 *
 * A DataProvider can be used to extract data from objects
 * that cannot be provided using the annotation system, for instance
 * becouse it uses an service not available within the entity
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
interface DataProviderInterface
{

    /**
     * Get the list of fields that are provided by this provider.
     *
     * Field names must be all lowercase, with optional underscore and seperated with a comma.
     * Fields can be global (no dot) or domain specific (with one(!) dot)
     *
     * @return array Fields provided by this provider
     */
    public function getProvidedFields();

    /**
     * Extract data from an object
     *
     * This function is responsible for testing if it can extract data from the
     * given class (by using instanceOf for example) and it should return an
     * array with CollectedData items
     *
     * @see \VisualM\DataExtractBundle\Data\CollectedData
     *
     * @param object $object Object
     *
     * @return array Extracted data
     */
    public function extractData($object);

}
