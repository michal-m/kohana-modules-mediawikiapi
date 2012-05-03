<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The MWAPI class
 *
 * @package     MediaWikiAPI
 * @author      Michał Musiał
 * @copyright   (c) 2012 Michał Musiał
 */
class MWAPI
{
    /**
     * @var  string  default instance name
     */
    public static $default = 'default';

    /**
     * @var  array  MWAPI instances
     */
    public static $instances = array();

    /**
     * Get a singleton MWAPI instance. If configuration is not specified,
     * it will be loaded from the api configuration file using the same group
     * as the name.
     *
     *     // Load the default api
     *     $mwapi = MWAPI::instance();
     *
     *     // Create a custom configured instance
     *     $mwapi = MWAPI::instance('custom', $config);
     *
     * @param   string   instance name
     * @param   array    configuration parameters
     * @return  MWAPI
     */
    public static function instance($name = NULL, array $config = NULL)
    {
        if ($name === NULL)
        {
            // Use the default instance name
            $name = MWAPI::$default;
        }

        if ( ! isset(MWAPI::$instances[$name]))
        {
            if ($config === NULL)
            {
                // Load the configuration for this MWAPI instance
                $config = Kohana::$config->load('mwapi')->$name;
            }

            // Create the MWAPI object
            new MWAPI($name, $config);
        }

        return MWAPI::$instances[$name];
    }

    // Instance name
    protected $_instance;

    // Configuration array
    protected $_config;

    // MediaWikiAPI url
    protected $_url;

    /**
     * Stores the api configuration locally and name the instance.
     *
     * [!!] This method cannot be accessed directly, you must use [MWAPI::instance].
     *
     * @return  void
     */
    protected function __construct($name, array $config)
    {
        // Set the instance name
        $this->_instance = $name;

        // Store the config locally
        $this->_config = $config;

        // Store the URL locally
        $this->_url = $config['url'];

        // Store the MWAPI instance
        MWAPI::$instances[$name] = $this;
    }

    /**
     * Returns the MWAPI instance name.
     *
     *     echo (string) $db;
     *
     * @return  string
     */
    final public function __toString()
    {
        return $this->_instance;
    }

    /**
     * Tries to log in via API using provided credentials
     *
     * @param   string  $username
     * @param   string  $password
     * @param   array   $config
     * @return  boolean Returns boolean true on success or false otherwise
     */
    public function login($username, $password, array $config = NULL)
    {
        return FALSE;
    }
}
