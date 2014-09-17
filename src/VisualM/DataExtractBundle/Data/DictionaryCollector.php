<?php
/**
 * Dictionary Collector
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Data;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use VisualM\DataExtractBundle\Annotation\DataElementInterface;
use VisualM\DataExtractBundle\Cache\CollectorCacheInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Dictionary Collector
 *
 * The collector scans Doctrine Entities for the DataElement annotation
 * and builds a dictonary of available fields

 * @author Elze Kool <info@visualmedia.nl>
 */
class DictionaryCollector implements DictionaryCollectorInterface, CacheWarmerInterface
{
    /**
     * Annotation Reader
     * @var Reader
     */
    protected $reader;

    /**
     * Doctrine Entity Manager
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * CollectorCache
     * @var CollectorCacheInterface
     */
    protected $cache;

    /**
     * Providers
     * @var array
     */
    protected $providers = [];

    /**
     * Constructor
     *
     * @param Reader                 $reader Reader
     * @param EntityManagerInterface $em     Doctrine Entity Manager
     */
    public function __construct(Reader $reader, EntityManagerInterface $em, CollectorCacheInterface $cache)
    {
        $this->reader = $reader;
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function addProvider(\VisualM\DataExtractBundle\Provider\DataProviderInterface $provider, $cachable = true)
    {
        $this->providers[] = [ 'cachable' => $cachable, 'provider' => $provider ];
    }

    /**
     * {@inheritDoc}
     */
    public function getProviders()
    {
        $providers = [];
        foreach ($this->providers as $provider) {
            $providers[] = $provider['provider'];
        }

        return $providers;
    }

    /**
     * {@inheritDoc}
     */
    public function getDoctrineProvidedFields()
    {
        // Check if field configurations are cached
        if ($this->cache->hasCache()) {
            $all_fields = $this->cache->getCache();
            $fields = [];
            foreach ($all_fields as $field) {
                if ($field instanceof DoctrineProvidedField) {
                    $fields[] = $field;
                }
            }

            return $fields;
        }

        $fields = [];

        // Go to Doctine Entities and fetch available fields
        $doctrine_meta = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($doctrine_meta as $m) {
            $class = $m->getName();
            $class_fields = $this->getFieldsForClass($class);
            $fields = array_merge($fields, $class_fields);
        }

        return $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableFields(array $types = null)
    {
        $fields = [];
        foreach ($this->getProvidedFields() as $field) {
            if ($types !== null and !in_array($field->getType(), $types)) {
                continue;
            }
            if (!in_array($field->getField(), $fields)) {
                $fields[] = $field->getField();
            }
        }

        // Sort fields on alphabet, where globals come first
        usort($fields, function ($a, $b) {
           if (strpos($a, '.') !== false and strpos($b, '.') === false) {
               return 1;
           } elseif (strpos($a, '.') === false and strpos($b, '.') !== false) {
               return -1;
           }

           return strcmp($a, $b);
        });

        return $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function isOptional()
    {
        // No saying how large an application that uses this library will be
        // so to prevent cache slam, make this warmer required, the time to
        // warm the cache isn't that long anyway
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function warmUp($cacheDir)
    {
        // Remove existing cache
        $this->cache->clearCache();

        // Trigger retrieval of Provided Fields
        // this will warmup the cache
        $this->getProvidedFields();
    }

    /**
     * Get All Fields that are provided, from providers and
     * from Doctrine
     *
     * @return array Provided Fields
     */
    protected function getProvidedFields()
    {
        if ($this->cache->hasCache()) {

            $fields = $this->cache->getCache();

            // Fetch ProvidedFields from Providers can't be cached
            foreach ($this->providers as $provider) {
                if ($provider['cachable']) { continue; }
                $provider_fields = $provider['provider']->getProvidedFields();
                $fields = array_merge($fields, $provider_fields);
            }

            return $fields;
        }

        // Fetch Fields that are provided by Doctrine Entities
        $fields = $this->getDoctrineProvidedFields();

        // Fetch ProvidedFields from Providers can be cached
        foreach ($this->providers as $provider) {
            if (!$provider['cachable']) { continue; }
            $provider_fields = $provider['provider']->getProvidedFields();
            $fields = array_merge($fields, $provider_fields);
        }

        $this->cache->storeInCache($fields);

        // Fetch ProvidedFields from Providers can't be cached
        foreach ($this->providers as $provider) {
            if ($provider['cachable']) { continue; }
            $provider_fields = $provider['provider']->getProvidedFields();
            $fields = array_merge($fields, $provider_fields);
        }

        return $fields;

    }

    /**
     * Get Fields for given Classname
     *
     * @param string $classname Classname
     *
     * @return array Fields provided by class
     */
    protected function getFieldsForClass($classname)
    {
        $fields = [];

        $reflection = new ReflectionClass($classname);
        $annotations = $this->reader->getClassAnnotations($reflection);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof DataElementInterface) {
                foreach ($annotation->getFields() as $field) {
                    $fields[] = new DoctrineProvidedField(
                        $field,
                        $annotation->getType(),
                        $classname,
                        $annotation->getGetter()
                    );
                }
            }
        }

        return $fields;
    }

}
