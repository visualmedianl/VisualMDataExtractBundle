<?php
/**
 * Data Collector
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use RuntimeException;

/**
 * Data Collector
 *
 * Objects can be pushed into the Data Collector and the Data Collector automaticly
 * collects the available data from it
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class DataCollector implements DataCollectorInterface
{
    /**
     * Dictionary Collector
     *
     * @var DictionaryCollectorInterface
     */
    protected $dictionaryCollector;

    /**
     * Collected data
     * @var array CollectedData elements
     */
    protected $collectedData = [];

    /**
     * Class Storage For Collected Data
     * @var array Classes
     */
    protected $collectedDataClass = [];

    /**
     * Constructor
     *
     * @param DictionaryCollectorInterface $dictionaryCollector DictionaryCollector
     */
    public function __construct(DictionaryCollectorInterface $dictionaryCollector)
    {
        $this->dictionaryCollector = $dictionaryCollector;
    }

    /**
     * {@inheritDoc}
     */
    public function pushObject($object, $ignoreNull = true)
    {
        if (!is_object($object)) {
            throw new RuntimeException("Expected an object");
        }

        $this->collectObjectDoctrine($object, $ignoreNull);
        $this->collectObjectProviders($object);
    }

    /**
     * {@inheritDoc}
     */
    public function getForSingleObject($object, $types = [ TypeEnum::STRING ], $ignoreNull = true)
    {
        if (!is_object($object)) {
            throw new RuntimeException("Expected an object");
        }

        $data = [];
        $data_types = [];

        // It was a design choice to iterate over the whole collection
        // of field configurations. It is posible to get the fields for a
        // single class based on it's annotation but that would make the
        // caching more difficult
        $fields = $this->dictionaryCollector->getDoctrineProvidedFields();

        foreach ($fields as $config) {

            // If type is not supported skip it
            if (!in_array($config->getType(), $types)) {
                continue;
            }

            // Check if field is available for this object
            if (!is_a($object, $config->getClassName())) {
                continue;
            }

            // Check if value is already available for given key, if so check
            // if current value is for a more prevered type
            if (
                isset($data_types[$config->getField()]) and
                array_search($config->getType(), $types) > array_search($data_types[$config->getField()], $types)
            ) {
                continue;
            }

            $getter = $config->getGetter();
            $value = $object->$getter();

            // Check if we need to skip null values
            if ($ignoreNull and ($value === null)) {
                continue;
            }

            $data_types[$config->getField()] = $config->getType();
            $data[$config->getField()] = $value;
        }

        return $this->unFlattenData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getCollected($types = [ TypeEnum::STRING ])
    {
        $data = [];
        foreach ($this->collectedData as $field_data) {
            foreach ($types as $type) {
                if ($field_data->hasData($type)) {
                    $data[$field_data->getField()] = $field_data->getData($type);
                    break;
                }
            }
        }

        return $this->unFlattenData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function clearCollected()
    {
        $this->collectedData = [];
        $this->collectedDataClass = [];
    }

    /**
     * Collect Fields that are provided from Doctrine for Object
     *
     * @param object  $object     Object
     * @param boolean $ignoreNull Ignore Null Values
     *
     * @return void
     */
    protected function collectObjectDoctrine($object, $ignoreNull)
    {
        if (!is_object($object)) {
            throw new RuntimeException("Expected an object");
        }

        // It was a design choice to iterate over the whole collection
        // of field configurations. It is posible to get the fields for a
        // single class based on it's annotation but that would make the
        // caching more difficult
        $fields = $this->dictionaryCollector->getDoctrineProvidedFields();

        foreach ($fields as $config) {

            // Check if field is available for this object
            if (!is_a($object, $config->getClassName())) {
                continue;
            }

            $getter = $config->getGetter();
            $value = $object->$getter();

            // Check if we need to skip null values
            if ($ignoreNull and ($value === null)) {
                continue;
            }

            if (
                array_key_exists($config->getField(), $this->collectedData) and
                $this->collectedDataClass[$config->getField()] == $config->getClassName()
            ) {
                // An existing field was discovered from the same class, append to existing
                $this->collectedData[$config->getField()]->addData($config->getType(), $value);

            } else {
                // A new field was discovered, just add it
                // An existing field was discovered from other class, clear current and add new
                $this->collectedData[$config->getField()] = new CollectedData($config->getField());
                $this->collectedData[$config->getField()]->addData($config->getType(), $value);
                $this->collectedDataClass[$config->getField()] = $config->getClassName();

            }
        }
    }

    /**
     * Collect Fields that are provided by Providers for the object
     *
     * @param object $object Object
     *
     * @return void
     */
    protected function collectObjectProviders($object)
    {
        if (!is_object($object)) {
            throw new RuntimeException("Expected an object");
        }

        $providers = $this->dictionaryCollector->getProviders();
        foreach ($providers as $provider) {
            $collected = $provider->extractData($object);
            foreach ($collected as $collected_field) {
                $this->collectedData[$collected_field->getField()] = $collected_field;
                $this->collectedDataClass[$collected_field->getField()] = null;
            }
        }
    }

    /**
     * Unflattens the flat array of data to a multi dimensional array
     * e.g. $['domain.value'] = 'val' becomes $['domain']['value'] = 'val'
     *
     * @param array $data Flattened data
     *
     * @return array Unflattened data
     */
    protected function unFlattenData($data)
    {
        $unflattened = [];
        foreach ($data as $key => $value) {
            if (strpos($key, '.') === false) {
                $unflattened[$key] = $value;
            } else {
                list($domain, $subkey) = explode('.', $key);
                if (!array_key_exists($domain, $unflattened)) {
                    $unflattened[$domain] = [];
                }
                $unflattened[$domain][$subkey] = $value;
            }
        }

        return $unflattened;
    }

}
