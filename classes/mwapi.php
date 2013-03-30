<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The MWAPI class
 *
 * @package     MediaWikiAPI
 * @author      Michał Musiał
 * @copyright   (c) 2013 Michał Musiał
 */
class MWAPI {

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

    /**
     * Instance name
     * @var string
     */
    protected $_instance;

    /**
     * Configuration array
     * @var string
     */
    protected $_config;

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

        // Build api URL
        $this->_config['url'] = ($config['secure']) ? 'https://' : 'http://' . $config['domain'] . '/' . $config['path'];

        // Store the MWAPI instance
        MWAPI::$instances[$name] = $this;
    }

    /**
     * Returns the MWAPI instance name.
     *
     *     echo (string) $mwapi;
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
     * @param   array   $params Extra params to be passed in the second request
     * @return  boolean Returns boolean true on success or false otherwise
     */
    public function login($username, $password, array $params = NULL)
    {
        // Perform the first, handshake request
        $request_1 = MWAPI_RequestBuilder::instance($this->_config['url'], 'login')
                ->method(Request::POST)
                ->param('lgname', $username)
                ->build();
        $response_1 = $request_1->execute();

        if ($response_1->status() >= 300)
            // throw new HTTP_Exception_500('Unsuccessful response from API: :status', array(':status' => $response_1->status()));
            return FALSE;

        $response_1_data = json_decode($response_1->body());

        if ($response_1_data->login->result != 'NeedToken')
            // throw new HTTP_Exception_500('Unexpected response from API: :result', array(':result' => $response_1_data->login->result));
            return FALSE;

        $cookie_prefix = $response_1_data->login->cookieprefix;
        $cookies = array(
            $cookie_prefix . '_session' => $response_1_data->login->sessionid,
        );

        // Perform the second, login request
        $rb_2 = MWAPI_RequestBuilder::instance($this->_config['url'], 'login')
                ->method(Request::POST)
                ->param('lgname', $username)
                ->param('lgpassword', $password)
                ->param('lgtoken', $response_1_data->login->token);

        if (is_array($params))
        {
            foreach ($params as $key => $value)
            {
                $rb_2->param($key, $value);
            }
        }

        $request_2 = $rb_2
                ->build()
                ->headers('Cookie', MWAPI_Cookie::build_cookie_header($cookies));
        $response_2 = $request_2->execute();

        if ($response_2->status() >= 300)
            // throw new HTTP_Exception_500('Unsuccessful response from API: :status', array(':status' => $response_2->status()));
            return FALSE;

        $response_2_data = json_decode($response_2->body());
        $wiki_cookies = MWAPI_Cookie::parse_cookie($response_2->headers('set-cookie'));

        if ($response_2_data->login->result != 'Success')
            // throw new HTTP_Exception_500('Unexpected response from API: :result', array(':result' => $response_2_data->login->result));
            return FALSE;

        // Send the Wiki Session cookies back to the client
        foreach ($wiki_cookies as $wiki_cookie)
        {
            setcookie($wiki_cookie['name'], $wiki_cookie['value'], $wiki_cookie['expires'], $wiki_cookie['path'], $this->_config['cookie-domain'], $this->_config['secure'], $wiki_cookie['httponly']);
        }

        return TRUE;
    }
}
