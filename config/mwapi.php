<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    'default' => array(
        /**
         * The following options must be set:
         *
         * string   domain          Domain name of the Wiki API
         * string   path            Path of the Wiki API
         * string   cookie-domain   Domain name for the Wiki Cookies (you cannot
         *                          set cookies on different domains)
         * bool     secure          Whether to access API via SSL
         *
         * @see http://www.mediawiki.org/wiki/API:Data_formats#Output
         */
        'domain'        => 'www.example.com',
        'path'          => 'wiki/api.php',
        'cookie-domain' => '.example.com',
        'secure'        => FALSE,
    ),
);
