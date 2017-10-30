<?php
/**
 * CRouter core class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * route
 * getCurrentUrl
 *
 *
 * STATIC:
 * ---------------------------------------------------------------
 * getParams
 *
 */

class CRouter
{
    /**	@var string */
    private $_controller;
    /**	@var string */
    private $_action;
    /**	@var string */
    private $_module;
    /** @var array */
    private static $_params = array();


    /**
     * Class constructor
     */
    public function __construct()
    {
         /* request url */
        $url = isset($_GET['url']) ? $_GET['url'] : '';

        $rules = CConfig::get('urlManager.rules');
        $urlFormat = CConfig::get('urlManager.urlFormat');
        if($urlFormat == 'shortPath' and is_array($rules)){
            foreach($rules as $rule => $value){
                $matches = '';
                if(preg_match_all('['.$rule.']i', $url, $matches)){
                    if(is_array($matches[0])){
                        foreach($matches[0] as $match => $val){
                            $value = str_ireplace('[$'.$match.']', $val, $value);
                        }
                        $url = $value;
                        break;
                    }
                }
            }
        }

        /* standard check */
        $split = explode('/', trim($url, '/'));
        if($split){
            foreach($split as $index => $part){
                if(!$this->_controller){
                    $this->_controller = ucfirst($part);
                    CDebug::addMessage('params', 'controller', $this->_controller);
                }else if(!$this->_action){
                    $this->_action = $part;
                    CDebug::addMessage('params', 'action', $this->_action);
                }else{
                    if(!self::$_params or end(self::$_params) !== null){
                        self::$_params[$part] = null;
                    }else{
                        self::$_params[end(array_keys(self::$_params))] = $part;
                    }
                    CDebug::addMessage('params', 'params', print_r(self::$_params, true));
                }
            }
        }

        if(!$this->_controller){
            $this->_controller = CFilter::sanitize('alphanumeric', CConfig::get('defaultController', 'Index'));
        }
        if(!$this->_action){
            $this->_action = CFilter::sanitize('alphanumeric', CConfig::get('defaultAction', 'index'));
        }
    }

    /**
     * Router
     */
    public function route()
    {
        $appDir = APP_PATH.DS.'protected'.DS.'controllers'.DS;
        $file = $this->_controller.'Controller.php';

        if(is_file($appDir.$file)){
            $class = $this->_controller.'Controller';
        }else{
            $comDir = APP_PATH.DS.'protected'.DS.CrypticBrain::app()->mapAppModule($this->_controller).'controllers'.DS;
            if(is_file($comDir.$file)){
                $class = $this->_controller.'Controller';
            }else{
                $class = 'ErrorController';
                CrypticBrain::app()->setResponseCode('404');
                CDebug::addMessage('errors', 'controller', CrypticBrain::t('core', 'Router: unable to resolve the request "{controller}".', array('{controller}' => $this->_controller)));
            }
        }

        CrypticBrain::app()->view->setController($this->_controller);
        $controller = new $class();
        if(is_callable(array($controller, $this->_action.'Action'))){
            $action = $this->_action.'Action';
        }else if($class != 'ErrorController'){
            $reflector = new ReflectionMethod($class, 'errorAction');
            if(!CAuth::isLoggedIn() and $reflector->getDeclaringClass()->getName() == 'CController'){
                $controller = new ErrorController();
                $action = 'indexAction';
            }else{
                $action = 'errorAction';
            }
            CDebug::addMessage('errors', 'action', CrypticBrain::t('core', 'The system is unable to find the requested action "{action}".', array('{action}' => $this->_action)));
        }else{
            $action = 'indexAction';
        }

        CrypticBrain::app()->view->setAction($this->_action);

        call_user_func_array(array($controller, $action), self::getParams());

        CDebug::addMessage('params', 'run_controller', $class);
        CDebug::addMessage('params', 'run_action', $action);
    }

    /**
     * Get array of parameters
     * @return array
     */
    public static function getParams()
    {
        return self::$_params;
    }

    /**
     * Returns current URL
     * @return string
     */
    public function getCurrentUrl()
    {
        $path = CrypticBrain::app()->getRequest()->getBaseUrl();
        $path .= strtolower(CrypticBrain::app()->view->getController()).'/';
        $path .= CrypticBrain::app()->view->getAction();

        $params = self::getParams();
        foreach($params as $key => $val){
            $path .= '/'.$key.'/'.$val;
        }

        return $path;
    }
}