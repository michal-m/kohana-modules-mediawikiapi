<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The MWAPI_RequestBuilder class
 *
 * @package     MediaWikiAPI
 * @author      Michał Musiał
 * @copyright   (c) 2013 Michał Musiał
 */
class MWAPI_Cookie {

    /**
     * Builds a Cookie header string based on values provided in $cookies param.
     * Values are automatically urlencode'd.
     *
     * @param   array   $cookies
     * @return  string
     */
    public static function build_cookie_header(array $cookies)
    {
        $cookie_pairs = array();

        foreach ($cookies as $key => $value)
        {
            $cookie_pairs[] = $key . '=' . urlencode($value);
        }

        return implode('; ', $cookie_pairs);
    }

    /**
     * Parses Set-Cookies headers
     *
     * @param   mixed   $cookies
     * @return  array
     */
    public static function parse_cookie($cookies)
    {
        if (is_array($cookies))
        {
            foreach ($cookies as &$cookie)
            {
                $cookie = self::parse_cookie($cookie);
            }

            return $cookies;
        }

        $tokens = explode('; ', $cookies);
        $cookie = array(
            'name'      => '',
            'value'     => '',
            'expires'   => 0,
            'path'      => '',
            'domain'    => '',
            'secure'    => FALSE,
            'httponly'  => FALSE,
        );

        foreach ($tokens as $token)
        {
            if (strpos($token, '=') !== FALSE)
            {
                $subtokens = explode('=', $token);

                switch (strtolower($subtokens[0]))
                {
                    case 'expires':
                        $cookie['expires'] = strtotime($subtokens[1]);
                    break;

                    case 'domain':
                    case 'path':
                        $cookie[strtolower($subtokens[0])] = $subtokens[1];
                    break;

                    // name and value
                    default:
                        $cookie['name'] = $subtokens[0];
                        $cookie['value'] = $subtokens[1];
                    break;
                }
            }
            else
            {
                $cookie[$token] = TRUE;
            }
        }

        return $cookie;
    }
}
