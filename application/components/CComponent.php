<?php
/**
 * CComponent is the base class for all components
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */

class CComponent
{
    /** @var array */
    private static $_components = array();

    /**
     * Class constructor
     * @return CComponent
     */
    function __construct()
    {
    }

    /**
     * Returns the static component of the specified class
     * @param CComponent|string $className
     * @return \class
     */
    public static function init($className = __CLASS__)
    {
        if(isset(self::$_components[$className])){
            return self::$_components[$className];
        }else{
            return self::$_components[$className] = new $className;
        }
    }
}