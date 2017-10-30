<?php
/**
 * CController base class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct                                          _getCalledClass
 * testAction
 * errorAction
 * redirect
 *
 * STATIC:
 * ---------------------------------------------------------------
 *
 */

class CController
{
    /** @var CView $view */
    protected $view;

    /**
     * Class constructor
     * @return \CController
     */
    public function __construct()
    {
        $this->view = CrypticBrain::app()->view;
    }

    /**
     * Renders error 404 view
     */
    public function errorAction()
    {
        $this->view->setMetaTags('title', 'Error 404');

        $errors = CDebug::getMessage('errors', 'action');
        if(is_array($errors)){
            foreach($errors as $error){
                $this->view->text .= $error;
            }
        }
        $this->view->render('error/index');
    }

    /**
     * Set session language
     */
    public function languageAction()
    {
        $language = CrypticBrain::app()->getRequest()->getQuery('code', 'string');
        if(in_array($language, array('en', 'jp'))){
            CrypticBrain::app()->setLanguage($language);
        }
        $this->redirect('index/index');
    }

    /**
     * Redirects to another controller
     * Parameter may consist from 2 parts: controller/action or just controller name
     * @param string $path
     * @return bool
     */
    public function redirect($path)
    {
        if(APP_MODE == 'test') return true;

        $controller = CConfig::get('defaultController', 'index');
        $action = CConfig::get('defaultAction', 'index');
        $paramsParts = explode('/', $path);
        $calledController = str_replace('controller', '', strtolower($this->_getCalledClass()));
        $params = '';
        $baseUrl = CrypticBrain::app()->getRequest()->getBaseUrl();

		if (strtolower($path) == 'index/index') {
			header('location: '.$baseUrl);
			exit;
		}
		
        if(!empty($path)){
            $parts = count($paramsParts);
            if($parts == 1){
                $controller = $calledController;
                $action = isset($paramsParts[0]) ? $paramsParts[0] : '';
            }else if($parts == 2){
                $controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
                $action = isset($paramsParts[1]) ? $paramsParts[1] : '';
            }else if($parts > 2){
                $controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
                $action = isset($paramsParts[1]) ? $paramsParts[1] : '';
                for($i=2; $i<$parts; $i++){
                    $params .= (isset($paramsParts[$i]) ? '/'.$paramsParts[$i] : '');
                }
            }
        }

        header('location: '.$baseUrl.$controller.'/'.$action.$params);
        exit;
    }

    /**
     * Refresh controller
     * @param int $delay
     * @return void
     */
    public function refresh($delay = 0)
    {
        header('Refresh: '.$delay);
        exit;
    }

    /**
     * Returns the name of called class
     * @return string|bool
     */
    private function _getCalledClass()
    {
        if(function_exists('get_called_class')) return get_called_class();
        $bt = debug_backtrace();
        if(!isset($bt[1])){
            return false;
        }else if(!isset($bt[1]['type'])){
            return false;
        }else switch ($bt[1]['type']) {
            case '::':
                $lines = file($bt[1]['file']);
                $i = 0;
                $callerLine = '';
                do{
                    $i++;
                    $callerLine = $lines[$bt[1]['line']-$i] . $callerLine;
                }while (stripos($callerLine,$bt[1]['function']) === false);
                preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/', $callerLine, $matches);
                if(!isset($matches[1])){
                    return false;
                }
                return $matches[1];
                break;
            case '->': switch ($bt[1]['function']) {
                case '__get':
                    if(!is_object($bt[1]['object'])){
                        return false;
                    }
                    return get_class($bt[1]['object']);
                default: return $bt[1]['class'];
            }
                break;
            default:
                return false;
                break;
        }
    }
}