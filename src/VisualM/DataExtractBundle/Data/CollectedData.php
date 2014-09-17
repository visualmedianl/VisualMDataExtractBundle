<?php
/**
 * Collected Data
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use RuntimeException;
use VisualM\DataExtractBundle\Data\TypeEnum;

/**
 * Collected Data
 *
 * Internaly used in the DataCollector to contain the information
 * that is collected when reading data from entities. Also used for
 * returning data from an DataProviderInterface.
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class CollectedData
{

    /**
     * Field Name
     * @var string
     */
    private $field;

    /**
     * Data, key is the type with the corresponding value
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param string $field Field name
     */
    public function __construct($field)
    {
        // Test field input for valid input
        // Field names must be all lowercase, with optional underscore and seperated with a comma.
        // Fields can be global (no dot) or domain specific (with one(!) dot)
        if (preg_match('/^([a-z_]+)(\.([a-z_]+))?$/', $field) == 0) {
            throw new RuntimeException(sprintf(
                'Invalid field name "%s" provided for "%s", ' .
                'field names must be all lowercase, with optional underscore and seperated with a comma, ' .
                'fields can be global (no dot) or domain specific (with one(!) dot)',
                $field,
                get_class($this)
            ));
        }

        $this->field = $field;
    }

    /**
     * Get Provided Field
     *
     * @return string Provided Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set Data
     *
     * If an value with the given type already exists it is overwritten
     *
     * @param string $type  Type (As available in TypeEnum)
     * @param mixed  $value Value
     *
     * @return CollectedData self
     */
    public function addData($type, $value)
    {
        if (!in_array($type, TypeEnum::getAll())) {
            throw new RuntimeException(sprintf(
                'Invalid type "%s" provided for setData in "@%s", available types are %s',
                $type,
                get_class($this),
                '"' . join('", "', TypeEnum::getAll()) . '"'
            ));
        }
        $this->data[$type] = $value;

        return $this;
    }

    /**
     * Check if Data is available for given type
     *
     * @param string $type Type (As available in TypeEnum)
     *
     * @return boolean Data available
     */
    public function hasData($type)
    {
        if (!in_array($type, TypeEnum::getAll())) {
            throw new RuntimeException(sprintf(
                'Invalid type "%s" provided for hasData in "@%s", available types are %s',
                $type,
                get_class($this),
                '"' . join('", "', TypeEnum::getAll()) . '"'
            ));
        }

        return array_key_exists($type, $this->data) ? true : false;
    }

    /**
     * Get Data of given Type
     *
     * @param string $type Type (As available in TypeEnum)
     *
     * @return mixed Data
     */
    public function getData($type)
    {
        if (!in_array($type, TypeEnum::getAll())) {
            throw new RuntimeException(sprintf(
                'Invalid type "%s" provided for getData in "@%s", available types are %s',
                $type,
                get_class($this),
                '"' . join('", "', TypeEnum::getAll()) . '"'
            ));
        }

        return array_key_exists($type, $this->data) ? $this->data[$type] : null;
    }

}
