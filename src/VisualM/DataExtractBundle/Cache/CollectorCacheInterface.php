<?php
/**
 * CollectorCache Interface
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
namespace VisualM\DataExtractBundle\Cache;

/**
 * CollectorCache Interface
 *
 * A CollectorCache caches the fields for the DictionaryCollector
 *
 * @author Elze Kool <info@visualmedia.nl>
 */
interface CollectorCacheInterface
{
    /**
     * Check if Cached data is available
     *
     * @return boolean Cache Available
     */
    public function hasCache();

    /**
     * Get Data from Cache
     *
     * @return array Cached field configuration
     */
    public function getCache();

    /**
     * Store Field Configuration in Cache
     *
     * @param array $fieldConfiguration Field Configuration
     *
     * @return void
     */
    public function storeInCache($fieldConfiguration);

    /**
     * Clear Cached Field Configuration (if available)
     *
     * @return void
     */
    public function clearCache();
}
