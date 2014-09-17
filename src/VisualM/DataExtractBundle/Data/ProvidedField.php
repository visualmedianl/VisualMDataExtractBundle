<?php
/**
 * Provided Field
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use RuntimeException;

/**
 * Provided Field
 *
 * Internaly used in the DictionaryCollector to store which fields are available.
 * Also used for returning fields from an DataProviderInterface.
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class ProvidedField
{
    /**
     * Field Name
     * @var string
     */
    protected $field;

    /**
     * Field Type
     * @var string
     */
    protected $type;

    /**
     * Constructor
     *
     * Field names must be all lowercase, with optional underscore and seperated with a comma.
     * Fields can be global (no dot) or domain specific (with one(!) dot)
     *
     * @param string $field Field Name
     * @param string $type  Field Type (As available in TypeEnum)
     */
    public function __construct($field, $type)
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

        if (!in_array($type, TypeEnum::getAll())) {
            throw new RuntimeException(sprintf(
                'Invalid type "%s" provided for "@%s", available types are %s',
                $type,
                get_class($this),
                '"' . join('", "', TypeEnum::getAll()) . '"'
            ));
        }

        $this->field = $field;
        $this->type = $type;
    }

    /**
     * Get Field Name
     *
     * @return string Field Name
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get Field Type
     *
     * @return string Field Type (as Provided by TypeEnum)
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Used when fetching object from cache, create object with
     * given state
     *
     * @param array $array State data
     *
     * @return ProvidedField ProvidedField
     */
    public static function __set_state($array)
    {
        return new self(
            isset($array['field']) ? $array['field'] : null,
            isset($array['type']) ? $array['type'] : null
        );
    }

}
