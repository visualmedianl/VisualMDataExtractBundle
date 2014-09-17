<?php
/**
 * DataElement Annotation Interface
 *
 * @author Elze Kool <info@visualmedia.nl>
 */

namespace VisualM\DataExtractBundle\Annotation;

/**
 * DataElement Annotation Interface
 *
 * Interface for annotation that indicates that Data can be Extracted from an Entity/Object instance
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
interface DataElementInterface
{

    /**
     * Constructor for Annotation
     *
     * @param array $values Values passed from Annotation
     */
    public function __construct(array $values);

    /**
     * Get list of DataFields that are provided
     *
     * @return array List of DataFields that are provided
     */
    public function getFields();

    /**
     * Get type of Data that is provided, default is a string
     *
     * @return string Type of Data that is provided, default is a string
     */
    public function getType();

    /**
     * Get getter function that is called when providing data
     *
     * @return string Getter function that is called when providing data
     */
    public function getGetter();

}
