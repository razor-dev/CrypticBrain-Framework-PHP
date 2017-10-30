<?php
/**
 * CrypticBrain bootstrap file
 *
 * PUBLIC:					PROTECTED:					    PRIVATE:
 * ----------               ----------                      ----------
 * __construct              _onBeginRequest                 _autoload
 * run                      _registerCoreComponents
 * getComponent             _setComponent
 * getRequest               _registerAppComponents
 * getSession               _registerAppModules
 * getCookie                _hasEvent
 * getCurl                  _hasEventHandler
 * attachEventHandler       _raiseEvent
 * detachEventHandler
 * mapAppModule
 * setResponseCode
 * getResponseCode
 * setLanguage
 * getLanguage
 * setTimeZone
 * getTimezone
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * app
 * powered
 * getVersion
 * t
 *
 */

class CrypticBrain
{
    /**	@var CView @view */
    public $view;
    /**	@var CRouter $router */
    public $router;
    /** @var string */
    public $charset = 'UTF-8';
    /** @var string */
    public $sourceLanguage = 'en';

    /** @var object */
    private static $_instance;
    /** @var array */
    private static $_classMap = array(
        'Controller'    => 'controllers',
        'Model'         => 'models',
        ''              => 'models',
    );
    /** @var array */
    private static $_coreClasses = array(
        'CComponent'     => 'components/CComponent.php',
        'CClientScript'  => 'components/CClientScript.php',
        'CConfig'        => 'components/CConfig.php',
        'CCurl'          => 'components/CCurl.php',
        'CHttpCookie'    => 'components/CHttpCookie.php',
        'CHttpRequest'   => 'components/CHttpRequest.php',
        'CHttpSession'   => 'components/CHttpSession.php',
        'CMessageSource' => 'components/CMessageSource.php',

        'CController'   => 'core/CController.php',
        'CDebug'        => 'core/CDebug.php',
        'CModel'        => 'core/CModel.php',
        'CRouter'       => 'core/CRouter.php',
        'CView'         => 'core/CView.php',

        'CActiveRecord' => 'db/CActiveRecord.php',
        'CDatabase'     => 'db/CDatabase.php',

        'CAuth'        => 'helpers/CAuth.php',
        'CCache'       => 'helpers/CCache.php',
        'CCaptcha'     => 'helpers/CCaptcha.php',
        'CEmail'       => 'helpers/CEmail.php',
        'CFile'        => 'helpers/CFile.php',
        'CFilter'      => 'helpers/CFilter.php',
        'CHash'        => 'helpers/CHash.php',
        'CHtml'        => 'helpers/CHtml.php',
        'CImage'       => 'helpers/CImage.php',
        'CIp'          => 'helpers/CIp.php',
        'CMailer'      => 'helpers/CMailer.php',
        'COctet'       => 'helpers/COctet.php',
        'CTime'        => 'helpers/CTime.php',
        'CTimeZone'    => 'helpers/CTimeZone.php',
        'CValidator'   => 'helpers/CValidator.php',
        'CWidget'      => 'helpers/CWidget.php',
    );
    /** @var array */
    private static $_coreComponents = array(
        'clientScript' => array('class' => 'CClientScript'),
        'cookie'       => array('class' => 'CHttpCookie'),
        'coreMessages' => array('class' => 'CMessageSource', 'language' => 'en'),
        'curl'         => array('class' => 'CCurl'),
        'messages'     => array('class' => 'CMessageSource'),
        'request'      => array('class' => 'CHttpRequest'),
        'session'      => array('class' => 'CHttpSession'),
    );

    /** @var array */
    private static $_appClasses = array(
        // empty
    );
    /** @var array */
    private static $_appComponents = array(
        // empty
    );
    /** @var array */
    private static $_appModules = array(
        // empty
    );
    /** @var array */
    private $_components = array();
    /** @var array */
    private $_events;
    /** @var boolean */
    private $_setup = false;
    /** @var string */
    private $_responseCode = '';
    /** @var string */
    private $_language;


    /**
     * Class constructor
     * @param array $configDir
     */
    public function __construct($configDir)
    {
        spl_autoload_register(array($this, '_autoload'));

        $configMain = $configDir.'main.php';
        $configDb = $configDir.'db.php';

        if(is_string($configMain) && is_string($configDb)){
            if(!file_exists($configMain)){
                $config = array(
                    'defaultTemplate' => 'setup',
                    'defaultController' => 'Setup',
                    'defaultAction' => 'index',
                );
                $url = isset($_GET['url']) ? $_GET['url'] : '';
                if(!preg_match('/setup\//i', $url)){
                    $_GET['url'] = 'setup/index';
                }
                $this->_setup = true;
            }else{
                $config = require($configMain);
                if(file_exists($configDb)){
                    $arrDbConfig = require($configDb);
                    $config = array_merge($config, $arrDbConfig);

                    foreach($config['modules'] as $module => $moduleInfo){
                        $configFile = APP_PATH.'/protected/modules/'.$module.'/config/main.php';
                        if(file_exists($configFile)){
                            $moduleConfig = include($configFile);
                            if(isset($moduleConfig['urlManager']['rules'])){
                                $rules = $moduleConfig['urlManager']['rules'];
                                $config['urlManager']['rules'] = array_merge($config['urlManager']['rules'], $rules);
                                unset($moduleConfig['urlManager']);
                            }
                            if(isset($moduleConfig['components'])){
                                $components = $moduleConfig['components'];
                                foreach($components as $id => $value){
                                    $components[$id]['module'] = $module;
                                }
                                $config['components'] = array_merge($config['components'], $components);
                                unset($moduleConfig['components']);
                            }
                            $config[$module] = $moduleConfig;
                        }
                    }
                }
            }
            /* save config */
            CConfig::load($config);
        }
    }

    /**
     * Runs application
     */
    public function run()
    {
        if(APP_MODE != 'off'){
            if(APP_MODE == 'debug' || APP_MODE == 'test'){
                error_reporting(E_ALL ^ E_STRICT);
                ini_set('display_errors', 'On');
            }else{
                error_reporting(E_ALL ^ E_STRICT);
                ini_set('display_errors', 'Off');
                ini_set('log_errors', 'On');
                ini_set('error_log', APP_PATH.DS.'protected'.DS.'tmp'.DS.'logs'.DS.'error.log');
            }

            if(CConfig::get('session.cacheLimiter') == 'private,must-revalidate'){
                // to prevent 'Web Page exired' message on using submission method 'POST'
                session_cache_limiter('private, must-revalidate');
            }

            // initialize CDebug class
            CDebug::init();

            date_default_timezone_set('Europe/Moscow');

            // load view (must do it before app components registration)
            $this->view = new CView();
            $this->view->setTemplate(CConfig::get('defaultTemplate'));
        }

        // register framework core components
        $this->_registerCoreComponents();
        // register application components
        $this->_registerAppComponents();
        // register application modules
        $this->_registerAppModules();

        // run events
        if($this->_hasEventHandler('_onBeginRequest')) $this->_onBeginRequest();

        // global test for database
        if(CConfig::get('db.driver') != ''){
            $db = CDatabase::init();
            if(!CAuth::isGuest()) $db->cacheOff();
        }

        if(APP_MODE != 'off'){
            $this->router = new CRouter();
            $this->router->route();
            CDebug::displayInfo();
        }
    }

    /**
     * Class init constructor
     * @param array $config
     * @return CrypticBrain
     */
    public static function init($config = array())
    {
        if(self::$_instance == null) self::$_instance = new self($config);
        return self::$_instance;
    }

    /**
     * Returns CrypticBrain object
     * @return CrypticBrain
     */
    public static function app()
    {
        return self::$_instance;
    }

    /**
     * Returns the version of application
     * @return string
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Returns a string
     * @return string
     */
    public static function powered()
    {
        return self::t('core', 'Powered by');
    }

    /**
     * Translates a message to the specified language
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $source
     * @param string $language
     * @return string
     */
    public static function t($category = 'app', $message = '', $params = array(), $source = null, $language = null)
    {
        /** @var CMessageSource $source */
        if($source===null) $source = ($category==='core') ? 'coreMessages' : 'messages';
        if(($source = self::$_instance->getComponent($source)) !== null){
            $message = $source->translate($category, $message, $language);
        }

        if($params===array()){
            return $message;
        }else{
            if(!is_array($params)) $params = array($params);
            return $params !== array() ? strtr($message, $params) : $message;
        }
    }


    /**
     * Autoloader
     * @param string $className
     * @return void
     */
    private function _autoload($className)
    {
        if(isset(self::$_coreClasses[$className])){
            include(dirname(__FILE__).DS.self::$_coreClasses[$className]);
        }else if(isset(self::$_appClasses[$className])){
            include(APP_PATH.DS.'protected'.DS.self::$_appClasses[$className]);
        }else{
            $classNameItems = preg_split('/(?=[A-Z])/', $className);
            $itemsCount = count($classNameItems);
            $pureClassName = $pureClassType = '';
            for($i = 0; $i < $itemsCount; $i++){
                if($i < $itemsCount - 1){
                    $pureClassName .= isset($classNameItems[$i]) ? $classNameItems[$i] : '';
                }else{
                    $pureClassType = isset($classNameItems[$i]) ? $classNameItems[$i] : '';
                }
            }

            if(!isset(self::$_classMap[$pureClassType])){
                $pureClassName = $className;
                $pureClassType = 'Model';
            }

            if(isset(self::$_classMap[$pureClassType])){
                $classCoreDir = APP_PATH.DS.'protected'.DS.self::$_classMap[$pureClassType];
                $classFile = $classCoreDir.DS.$className.'.php';
                if(is_file($classFile)){
                    include($classFile);
                }else{
                    $classModuleDir = APP_PATH.DS.'protected'.DS.$this->mapAppModule($pureClassName).self::$_classMap[$pureClassType];
                    $classFile = $classModuleDir.DS.$className.'.php';
                    if(is_file($classFile)){
                        include($classFile);
                    }else{
                        CDebug::addMessage('errors', 'missing-model', CrypticBrain::t('core', 'Unable to find class "{class}".', array('{class}'=>$className)), 'session');
                        //header('location: '.$this->getRequest()->getBaseUrl().'error/index/code/500');
                        //exit;
                    }
                }
                CDebug::addMessage('general', 'classes', $className);
            }
        }
    }

    /**
     * Puts a component under the management of the application
     * @param string $id
     * @param CComponent $component
     */
    protected function _setComponent($id, $component)
    {
        if($component===null){
            unset($this->_components[$id]);
        }else{
            if($callback = $component::init()){
                $this->_components[$id] = $callback;
            }else{
                CDebug::addMessage('warnings', 'missing-components', $component);
            }
        }
    }

    /**
     * Returns the application component
     * @param string $id
     * @return component|null
     */
    public function getComponent($id)
    {
        return (isset($this->_components[$id])) ? $this->_components[$id] : null;
    }

    /**
     * Returns the client script component
     * @return CClientScript component
     */
    public function getClientScript()
    {
        return $this->getComponent('clientScript');
    }

    /**
     * Returns the request component
     * @return CHttpRequest component
     */
    public function getRequest()
    {
        return $this->getComponent('request');
    }

    /**
     * Returns the session component
     * @return CHttpSession or CDbHttpSession component
     */
    public function getSession()
    {
        return $this->getComponent('session');
    }

    /**
     * Returns the cookie component
     * @return CHttpCookie component
     */
    public function getCookie()
    {
        return $this->getComponent('cookie');
    }

    /**
     * Returns the curl component
     * @return CCurl component
     */
    public function getCurl()
    {
        return $this->getComponent('curl');
    }

    /**
     * Attaches event handler
     * @param string $name
     * @param string $handler
     */
    public function attachEventHandler($name, $handler)
    {
        if($this->_hasEvent($name)){
            $name = strtolower($name);
            if(!isset($this->_events[$name])){
                $this->_events[$name] = array();
            }
            if(!in_array($handler, $this->_events[$name])){
                $this->_events[$name][] = $handler;
            }
        }else{
            CDebug::addMessage('errors', 'events-attach', CrypticBrain::t('core', 'Event "{class}.{name}" is not defined.', array('{class}'=>get_class($this), '{name}'=>$name)));
        }
    }

    /**
     * Detaches event handler
     * @param string $name
     */
    public function detachEventHandler($name)
    {
        if($this->_hasEvent($name)){
            $name = strtolower($name);
            if(isset($this->_events[$name])){
                unset($this->_events[$name]);
            }
        }else{
            CDebug::addMessage('errors', 'events-detach', CrypticBrain::t('core', 'Event "{class}.{name}" is not defined.', array('{class}'=>get_class($this), '{name}'=>$name)));
        }
    }

    /**
     * Checks whether an event is defined
     * An event is defined if the class has a method named like 'onSomeMethod'
     * @param string $name
     * @return boolean
     */
    protected function _hasEvent($name)
    {
        return !strncasecmp($name, '_on', 3) && method_exists($this, $name);
    }

    /**
     * Checks whether the named event has attached handlers
     * @param string $name
     * @return boolean
     */
    public function _hasEventHandler($name)
    {
        $name = strtolower($name);
        return isset($this->_events[$name]) && count($this->_events[$name]) > 0;
    }

    /**
     * Raises an event
     * @param string $name
     */
    public function _raiseEvent($name)
    {
        $name = strtolower($name);
        if(isset($this->_events[$name])){
            foreach($this->_events[$name] as $handler){
                if(is_string($handler[1])){
                    call_user_func_array(array($handler[0], $handler[1]), array());
                }else{
                    CDebug::addMessage('errors', 'events-raising', CrypticBrain::t('core', 'Event "{{class}}.{{name}}" is attached with an invalid handler "{'.$handler[1].'}".', array('{class}'=>$handler[0], '{name}'=>$handler[1])));
                }
            }
        }
    }

    /**
     * Maps application modules
     * @param string $class
     * @return string
     */
    public function mapAppModule($class)
    {
        foreach(self::$_appModules as $module => $moduleInfo){
            if(!isset($moduleInfo['classes'])) continue;
            if(in_array(strtolower($class), array_map('strtolower', $moduleInfo['classes']))){
                return 'modules/'.$module.'/';
            }
        }
        return '';
    }

    /**
     * Sets response code
     * @param string $code
     */
    public function setResponseCode($code = '')
    {
        $this->_responseCode = $code;
    }

    /**
     * Get response code
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }

    /**
     * Set language
     * @param string $code
     */
    public function setLanguage($code = '')
    {
        $this->_language = $code;
        $this->getSession()->set('language', $this->_language);
    }

    /**
     * Returns the language that is used for application or language parameter
     * @return string
     */
    public function getLanguage()
    {
        $language = $this->getSession()->get('language');
        if(!empty($language)){
            return $language;
        }else{
            return $this->_language===null ? $this->sourceLanguage : $this->_language;
        }
    }

    /**
     * Sets the time zone used by the application
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        if(empty($timezone)) $timezone = CConfig::get('defaultTimeZone');

        $this->_timezone = $timezone;
        $this->getSession()->set('timezone', $this->_timezone);
        date_default_timezone_set($this->_timezone);
    }

    /**
     * Returns the time zone used by the application
     * @return string
     */
    public function getTimezone()
    {
        $timezone = $this->getSession()->get('timezone');
        if(!empty($timezone)){
            return $timezone;
        }else{
            return date_default_timezone_get();
        }
    }

    /**
     * Raised before the application processes the request
     */
    protected function _onBeginRequest()
    {
        $this->_raiseEvent('_onBeginRequest');
    }

    /**
     * Registers core components
     */
    protected function _registerCoreComponents()
    {
        foreach(self::$_coreComponents as $id => $component){
            $this->_setComponent($id, $component['class']);
        }
    }

    /**
     * Registers application components
     */
    protected function _registerAppComponents()
    {
        if(!is_array(CConfig::get('components'))) return false;
        foreach(CConfig::get('components') as $id => $component){
            $enable = isset($component['enable']) ? $component['enable'] : false;
            $class = isset($component['class']) ? $component['class'] : '';
            $module = isset($component['module']) ? $component['module'] : '';
            if($enable and $class){
                self::$_appComponents[$id] = $class;
                self::$_appClasses[$class] = (!empty($module) ? 'modules/'.$module.'/' : '').'components/'.$class.'.php';
                $this->_setComponent($id, $class);
            }
        }
        return true;
    }

    /**
     * Registers application modules
     */
    protected function _registerAppModules()
    {
        if(!is_array(CConfig::get('modules'))) return false;
        foreach(CConfig::get('modules') as $id => $module){
            if($module['enable']){
                self::$_appModules[$id]['classes'] = $module['classes'];
            }
        }
        return true;
    }
}