<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The MWAPI_RequestBuilder class
 *
 * @package     MediaWikiAPI
 * @author      Michał Musiał
 * @copyright   (c) 2012 Michał Musiał
 */
class MWAPI_RequestBuilder
{
    /**
     * Get a singleton MWAPI_RequestBuilder instance.
     *
     *     // Load the default api
     *     $mwapi_rb = MWAPI_RequestBuilder::instance($url);
     *
     *     // Create a custom configured instance
     *     $mwapi_rb = MWAPI_RequestBuilder::instance($url, 'xml');
     *
     * @param   string   API URL
     * @param   string   action name
     * @param   string   configuration parameters
     * @return  MWAPI_RequestBuilder
     *
     * @see http://www.mediawiki.org/wiki/API:Data_formats#Output
     */
    public static function instance($url, $action, $format = 'json')
    {
        // Create the MWAPI_RequestBuilder object
        return new MWAPI_RequestBuilder($url, $action, $format);
    }

	/**
	 * @var  string  The HTTP request method
	 */
	protected $_method = Request::GET;

	/**
	 * @var  string  the URL of the request
	 */
	protected $_url;

	/**
	 * @var  array   parameters from the route
	 */
	protected $_params = array();

    /**
     * @return  void
     */
    protected function __construct($url, $action)
    {
        // Store the URL locally
        $this->_url = $url;

        // Set the action parameter
        $this->param('action', $action);

        // Set the format parameter
        $this->param('format', 'json');
    }

    /**
     * Returns the Request URL.
     *
     *     echo (string) $mwapi_rb;
     *
     * @return  string
     */
    final public function __toString()
    {
        return $this->_url . URL::query($this->_params, FALSE);
    }

    /**
     * Returns a string represe
     * @return Request
     */
    public function build()
    {
        return Request::factory($this->_url)->method($this->_method)->query($this->_params);
    }

    /**
     * Gets or sets the HTTP method.
     *
     * @param type $method
     * @return mixed
     */
    public function method($method = NULL)
    {
        if ($method === NULL)
        {
            // Act as a getter
            return $this->_method;
        }

        // Act as a setter
        $this->_method = strtoupper($method);

        return $this;
    }

    /**
     * Sets or returns the request parameters
     *
     * @param   string  $key
     * @param   string  $value
     * @param   mixed   $default
     * @return  mixed
     */
    public function param($key = NULL, $value = NULL, $default = NULL)
    {
        if ($key === NULL)
        {
            // Return the full array
            return $this->_params;
        }

        if ($value !== NULL)
        {
            // Set the parameter
            $this->_params[$key] = $value;
            return $this;
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }
}
