<?php
/**
 * CHttpCookie provides cookie-level data management
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * set
 * get
 * remove
 * clear
 * setDomain
 * setPath
 * getAll
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */

class CHttpCookie extends CComponent
{
    /** @var integer
     * Default 0 means - until the browser is closed
     */
    public $expire = 0;
    /** @var boolean */
    public $secure = false;
    /** @var boolean */
    public $httpOnly = true;

    /** @var string */
    private $_domain = '';
    /** @var string */
    private $_path = '/';

    /**
     * Class default constructor
     */
    function __construct()
    {
        if(CConfig::get('cookies.domain') != '') $this->setDomain(CConfig::get('cookies.domain'));
        if(CConfig::get('cookies.path') != '') $this->setPath(CConfig::get('cookies.path'));
    }

    /**
     * Returns the instance of object
     * @return CHttpCookie class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Sets cookie domain
     * @param string $domain
     */
    public function setDomain($domain = '')
    {
        $this->_domain = $domain;
    }

    /**
     * Sets cookie path
     * @param string $path
     */
    public function setPath($path = '')
    {
        $this->_path = $path;
    }

    /**
     * Sets cookie
     * @param string $name
     * @param mixed $value
     * @param mixed $expire
     * @param mixed $path
     * @param mixed $domain
     */
    public function set($name, $value = '', $expire = '', $path = '', $domain = '')
    {
        $expire = (!empty($expire)) ? $expire : $this->expire;
        $path = (!empty($path)) ? $path : $this->_path;
        $domain = (!empty($domain)) ? $domain : $this->_domain;

        setcookie($name, $value, $expire, $path, $domain, $this->secure, $this->httpOnly);
    }

    /**
     * Returns cookie value
     * @param string $name
     * @return string
     */
    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    }

    /**
     * Deletes cookie
     * @param string $name
     */
    public function remove($name)
    {
        setcookie($name, null, 0, $this->_path, $this->_domain, $this->secure, $this->httpOnly);
    }

    /**
     * Deletes all cookie
     * @return string|void
     */
    public function clear()
    {
        if(!isset($_COOKIE)) return '';

        foreach($_COOKIE as $key => $value){
            $this->remove($value);
        }
    }

    /**
     * Get all cookies
     * @return array
     */
    public function getAll()
    {
        return isset($_COOKIE) ? $_COOKIE : array();
    }
}