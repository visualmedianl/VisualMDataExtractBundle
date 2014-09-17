<?php
/**
 * CollectorCache
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Cache;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * CollectorCache Interface
 *
 * A CollectorCache caches the available fields for the DictionaryCollector
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
class CollectorCache implements CollectorCacheInterface, CacheClearerInterface
{
    /**
     * Debugging enabled (disables cache)
     * @var boolean
     */
    protected $debug;

    /**
     * Cache File
     * @var string
     */
    protected $cacheFile;

    /**
     * In Memory Cache if Field Configuation
     * @var array
     */

    protected $fieldConfiguration;

    /**
     * Constructor
     *
     * @param string  $cacheDir Caching Directory
     * @param boolean $debug    Debugging enabled
     */
    public function __construct($cacheDir, $debug)
    {
        $this->cacheFile = $cacheDir . DIRECTORY_SEPARATOR . 'visualMDataDictionaryCache.php';
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function hasCache()
    {
        // Disable caching in debug mode
        if ($this->debug) { return false; }

        // Check if field configuration is loaded or that cache file exists
        if ($this->fieldConfiguration !== null or file_exists($this->cacheFile )) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getCache()
    {
        // Check if field configuration is loaded in memory
        if ($this->fieldConfiguration !== null) {
            return $this->fieldConfiguration;
        }

        // If not load from cache file
        require $this->cacheFile;

        return $this->fieldConfiguration = $fieldConfiguration;

    }

    /**
     * {@inheritDoc}
     */
    public function storeInCache($fieldConfiguration)
    {
        // Disable caching in debug mode
        if ($this->debug) { return; }

        // Store fieldConfiguration in file and memory
        $file_contents = '<?php $fieldConfiguration = ' . var_export($fieldConfiguration, true) . ';';
        file_put_contents($this->cacheFile, $file_contents);
        $this->fieldConfiguration = $fieldConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function clearCache()
    {
        if (file_exists($this->cacheFile )) {
            unlink($this->cacheFile);
        }
        $this->fieldConfiguration = null;
    }

    /**
     * {@inheritDoc}
     */
    public function clear($cacheDir)
    {
        $this->clearCache();
    }

}
