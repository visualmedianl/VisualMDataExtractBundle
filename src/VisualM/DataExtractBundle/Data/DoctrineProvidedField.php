<?php
/**
 * Doctrine Provided Field
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use RuntimeException;

/**
 * Doctrine Provided Field
 *
 * Internaly used in the DictionaryCollector to store which fields are available
 * in Doctrine Entities
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class DoctrineProvidedField extends ProvidedField
{
    /**
     * Class Name
     * @var string
     */
    protected $className;

    /**
     * Getter
     * @var string
     */
    protected $getter;

    /**
     * Constructor
     *
     * Field names must be all lowercase, with optional underscore and seperated with a comma.
     * Fields can be global (no dot) or domain specific (with one(!) dot)
     *
     * @param string $field     Field Name
     * @param string $type      Field Type (As available in TypeEnum)
     * @param string $className Class Name where provided Field is defined
     * @param string $getter    Getter Function
     *
     */
    public function __construct($field, $type, $className, $getter)
    {
        parent::__construct($field, $type);

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $getter) == 0) {
            throw new RuntimeException(sprintf(
                'Invalid class name "%s" provided for  "@%s".',
                $className,
                get_class($this)
            ));
        }

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $getter) == 0) {
            throw new RuntimeException(sprintf(
                'Invalid getter function "%s" provided for  "@%s".',
                $getter,
                get_class($this)
            ));
        }

        $this->className = $className;
        $this->getter = $getter;
    }

    /**
     * Get Class Name where provided Field is defined
     *
     * @return string Class Name
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get Getter that should be called on Class
     *
     * @return string Getter
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * Used when fetching object from cache, create object with
     * given state
     *
     * @param array $array State data
     *
     * @return DoctrineProvidedField DoctrineProvidedField
     */
    public static function __set_state($array)
    {
        return new self(
            isset($array['field']) ? $array['field'] : null,
            isset($array['type']) ? $array['type'] : null,
            isset($array['className']) ? $array['className'] : null,
            isset($array['getter']) ? $array['getter'] : null
        );
    }

}
