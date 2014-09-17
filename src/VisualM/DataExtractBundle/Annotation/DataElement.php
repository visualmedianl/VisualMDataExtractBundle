<?php
/**
 * DataElement Annotation
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Annotation;

use RuntimeException;
use VisualM\DataExtractBundle\Data\TypeEnum;

/**
 * DataElement Annotation
 *
 * Use this annotation to indicate that Data can be Extracted from an Entity/Object instance
 *
 * @Annotation
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class DataElement implements DataElementInterface
{

    /**
     * Type of Data that is provided, default is a string
     * @var string
     */
    protected $type = TypeEnum::STRING;

    /**
     * List of DataFields that are provided
     * @var array
     */
    protected $fields;

    /**
     * Getter function that is called when providing data
     * @var string
     */
    protected $getter;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $values)
    {
        // Check if required keys are provided
        foreach (['fields', 'getter'] as $key) {
            if (empty($values[$key])) {
                throw new RuntimeException(sprintf('Mandatory key "%s" for annotation "@%s" not provided.', $key, get_class($this)));
            }
        }

        // Call setter for keys
        foreach ($values as $key => $val) {
            $setter = 'set'. ucfirst($key);
            if (!method_exists($this, $setter)) {
                throw new RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $key, get_class($this)));
            }
            $this->$setter($val);
        }
    }

    /**
     * Set type of Data that is provided, default is a string
     *
     * @param string $type Type of Data that is provided, default is a string
     *
     * @return void
     */
    protected function setType($type)
    {
        // Check if valid type
        if (!in_array($type, TypeEnum::getAll())) {
            throw new RuntimeException(sprintf(
                'Invalid type "%s" provided for annotation "@%s", available types are %s',
                $type,
                get_class($this),
                '"' . join('", "', TypeEnum::getAll()) . '"'
            ));
        }
        $this->type = $type;
    }

    /**
     * Set list of DataFields that are provided
     *
     * @param string $fields Raw (string) list of DataFields that are provided
     *
     * @return void
     */
    protected function setFields($fields)
    {
        // Test fields input for valid input
        // Field names must be all lowercase, with optional underscore and seperated with a comma.
        // Fields can be global (no dot) or domain specific (with one(!) dot)
        if (preg_match('/^([a-z_]+)(\.([a-z_]+))?(\s*,\s*([a-z_]+)(\.([a-z_]+))?)*$/', $fields) == 0) {
            throw new RuntimeException(sprintf(
                'Invalid fields configuration "%s" provided for annotation "@%s", ' .
                'field names must be all lowercase, with optional underscore and seperated with a comma, ' .
                'fields can be global (no dot) or domain specific (with one(!) dot)',
                $fields,
                get_class($this)
            ));
        }

        $this->fields = [];
        foreach (explode(',', $fields) as $field) {
            $this->fields[] = trim($field);
        }
    }

    /**
     * Set getter function that is called when providing data
     *
     * @param string $getter Getter function that is called when providing data
     *
     * @return void
     */
    protected function setGetter($getter)
    {
        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $getter) == 0) {
            throw new RuntimeException(sprintf(
                'Invalid getter function "%s" provided for annotation "@%s".',
                $getter,
                get_class($this)
            ));
        }
        $this->getter = $getter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getGetter()
    {
        return $this->getter;
    }
}
