<?php
/**
 * TypeEnum
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

/**
 * TypeEnum
 *
 * Defines the available data types for Data Elements
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class TypeEnum
{

    /**
     * Regular String Contents
     */
    const STRING = 'string';

    /**
     * Integer Number
     */
    const INTEGER = 'int';

    /**
     * Floating Point Number
     */
    const FLOAT = 'float';

    /**
     * Date/Time object
     */
    const DATETIME = 'datetime';

    /**
     * Get all Defined Constants
     *
     * @return array Defined Constants
     */
    public static function getAll()
    {
        return [
            self::STRING,
            self::INTEGER,
            self::FLOAT,
            self::DATETIME
        ];
    }

}
