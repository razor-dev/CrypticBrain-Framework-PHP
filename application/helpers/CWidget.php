<?php
/**
 * CWidget is a helper class file that represents base class for all widgets classes
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * create
 * 
 */

class CWidget
{

    /**
     * Creates appropriate widget
     * @param string $className
     * @param array $params
     * @return mixed
     */
	public static function create($className, $params = array())
    {
        include_once(__DIR__.'/widgets/'.$className.'.php');

        if (!class_exists($className)) {
            CDebug::addMessage('warnings', 'missing-helper', CrypticBrain::t('core', 'Cannot find widget class: {class}', array('{class}' => $className)));
        } else {
            return $className::init($params);
        }
    }
}