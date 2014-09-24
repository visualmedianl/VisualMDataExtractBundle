<?php
/**
 * Data Collector Interface
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

/**
 * Data Collector Interface
 *
 * Objects can be pushed into a Data Collector and the Data Collector automaticly
 * collects the available data from it
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
interface DataCollectorInterface
{
    /**
     * Push Object Data to Collection
     *
     * @param object  $object     Object to collect data for
     * @param boolean $ignoreNull Ignore null values
     *
     * @return void
     */
    public function pushObject($object, $ignoreNull = true);

    /**
     * Collect Data for Single Object
     *
     * Collects data for Single Object and returns array of values that adhere
     * to the given type. If more than one type if available for a given key
     * the ordering of types determine the value returned
     *
     * @param object  $object     Object to extract data for
     * @param array   $types      Types of data to return
     * @param boolean $ignoreNull Ignore null values
     *
     * @return array Extracted data
     */
    public function getForSingleObject($object, $types = [ TypeEnum::STRING ], $ignoreNull = true);

    /**
     * Get Collected Data
     *
     * Returns array of values collected from objects that adhere
     * to the given type. If more than one type if available for a given key
     * the ordering of types determine the value returned
     *
     * @param array $types Types of data to return
     *
     * @return Collected data
     */
    public function getCollected($types = [ TypeEnum::STRING ]);

    /**
     * Clear storage for collected data
     *
     * @return void
     */
    public function clearCollected();

}
