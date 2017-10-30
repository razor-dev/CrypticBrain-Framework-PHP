<?php
/**
 * CCurl a base component for processing CURL REQUESTS
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------			   ----------				  ----------
 * __construct				setOption				  handleCurl
 * init						setDefaults
 * setUrl
 * setCookies
 * setHttpLogin
 * setProxy
 * setProxyLogin
 * hasErrors
 * getData
 * run
 *
 * STATIC:
 * ---------------------------------------------------------------
 *
 */

class CCurl extends CComponent
{
    /** @var string */
    protected $url;
    /** @var object */
    protected $ch;

    /** @var array */
    public $options = array();

    /** @var int */
    private $error_code = 0;
    /** @var string */
    private $data = null;

    /**
     * Class default constructor
     */
    function __construct()
    {
        if(!function_exists('curl_init')){
            CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'You must have CURL enabled in order to use Curl extension.'));
        }
    }

    /**
     *	Returns the instance of object
     *	@return CCurl class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Format url
     * @param $url
     * @internal param
     */
    public function setUrl($url)
    {
        if(!preg_match('!^\w+://! i', $url)){
            $url = 'http://' . $url;
        }
        $this->url = $url;
    }

    /**
     * Set cookies
     * @param $values
     * @return void
     */
    public function setCookies($values)
    {
        if(!is_array($values)){
            CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'Options must be an array.'));
        }else{
            $params = $this->cleanPost($values);
        }
        $this->setOption(CURLOPT_COOKIE, $params);
    }

    /**
     * Setup http login
     * @param string $username
     * @param string $password
     * @return void
     */
    public function setHttpLogin($username = '', $password = '')
    {
        $this->setOption(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set proxy
     * @param $url
     * @param int $port
     * @return void
     */
    public function setProxy($url, $port = 80)
    {
        $this->setOption(CURLOPT_HTTPPROXYTUNNEL, true);
        $this->setOption(CURLOPT_PROXY, $url . ':' . $port);
    }

    /**
     * Set proxy login
     * @param string $username
     * @param string $password
     * @return void
     */
    public function setProxyLogin($username = '', $password = '')
    {
        $this->setOption(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
    }

    /**
     * Set any extra option
     * @param $key
     * @param $value
     * @return void
     */
    protected function setOption($key, $value)
    {
        curl_setopt($this->ch, $key, $value);
    }

    /**
     * Default options
     * @return void
     */
    protected function setDefaults()
    {
        if(!isset($this->options['timeout'])){
            $this->setOption(CURLOPT_TIMEOUT, 30);
        }
        if(!isset($this->options['setOptions'][CURLOPT_HEADER])){
            $this->setOption(CURLOPT_HEADER, false);
        }
        if(!isset($this->options['setOptions'][CURLOPT_ENCODING])){
            $this->setOption(CURLOPT_ENCODING, CrypticBrain::app()->charset);
        }
        if(!isset($this->options['setOptions'][CURLOPT_RETURNTRANSFER])){
            $this->setOption(CURLOPT_RETURNTRANSFER, true);
        }
        if(!isset($this->options['setOptions'][CURLOPT_FOLLOWLOCATION])){
            $this->setOption(CURLOPT_FOLLOWLOCATION, false);
        }
        if(!isset($this->options['setOptions'][CURLOPT_FAILONERROR])){
            $this->setOption(CURLOPT_FAILONERROR, true);
        }
    }

    /**
     * Has error
     * @return bool
     */
    public function hasErrors()
    {
        return $this->error_code !== 0;
    }

    /**
     * Return data
     * @param bool $decode
     * @return string
     */
    public function getData($decode = false)
    {
        return ($decode===false) ? $this->data : json_decode($this->data, true);
    }

    /**
     * Handle curl
     * @param $url
     * @param array $postData
     * @return $this
     */
    private function handleCurl($url, array $postData)
    {
        $this->setUrl($url);

        if(!$this->url){
            CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'You must set Url.'));
        }

        $this->ch = curl_init();

        if(empty($postData)){
            $this->setOption(CURLOPT_URL, $this->url);
            $this->setDefaults();
        }else{
            if(!is_array($postData)){
                CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', '$postData must be an array type.'));
            }
            $this->setOption(CURLOPT_URL, $this->url);
            $this->setDefaults();
            $this->setOption(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            $this->setOption(CURLOPT_POST, true);
            $this->setOption(CURLOPT_POSTFIELDS, $postData);
        }

        if(isset($this->options['setOptions'])){
            foreach($this->options['setOptions'] as $k => $v){
                $this->setOption($k, $v);
            }
        }
        if(isset($this->options['login'])){
            $this->setHttpLogin($this->options['login']['username'], $this->options['login']['password']);
        }
        if(isset($this->options['proxy'])){
            $this->setProxy($this->options['proxy']['url'], $this->options['proxy']['port']);
        }
        if(isset($this->options['proxylogin'])){
            if(!isset($this->options['proxy'])){
                CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'If you use "proxylogin", you must define "proxy" with arrays.'));
            }else{
                $this->setProxyLogin($this->options['login']['username'], $this->options['login']['password']);
            }
        }

        $this->data = curl_exec($this->ch);
        if($this->data === false or $this->data === null){
            $this->error_code = curl_errno($this->ch);
            CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'Error code - {code}. Error message - {message}.', array('{code}'=>$this->error_code, '{message}'=>curl_error($this->ch))));
        }else{
            CDebug::addMessage('server', 'curl', curl_getinfo($this->ch));
        }
        curl_close($this->ch);

        return $this;
    }

    /**
     * Running curl
     * @param $url
     * @param array $postData
     * @return mixed
     */
    public function run($url, array $postData = array())
    {
        if(!CValidator::isUrl($url)){
            CDebug::addMessage('errors', 'curl', CrypticBrain::t('core', 'The parameter $url must be a valid URL string value!'));
            return false;
        }

        $reflection = new ReflectionMethod(get_class($this), 'handleCurl');
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($this, array($url, $postData));
    }
}