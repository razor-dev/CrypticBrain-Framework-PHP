<?php
/**
 * CAuth is a helper class that provides basic authentication methods
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * isLoggedIn
 * isLoggedInAs
 * isLoggedInAsAdmin
 * isGuest
 * handleLogin
 * handleLoggedIn
 * getLoggedId
 * getLoggedName
 * getLoggedEmail
 * getLoggedRole
 * getLoggedParam
 *
 */

class CAuth
{

    /**
     * Checks if user is logged in and returns a result
     * @return bool
     */
    public static function isLoggedIn()
    {
        return (CrypticBrain::app()->getSession()->get('loggedIn') == true) ? true : false;
    }

    /**
     * Checks if user is logged in as a specific account role
     * @param array $roles
     * @return bool
     */
    public static function isLoggedInAs($roles = array())
    {
        if(!self::isLoggedIn()) return false;

        $loggedRole = self::getLoggedRole();
        if(empty($roles)) $roles = func_get_args();
        if(in_array($loggedRole, $roles)){
            return true;
        }
        return false;
    }

    /**
     * Checks if user is logged in as an admin
     * @return bool
     */
    public static function isLoggedInAsAdmin()
    {
        if(!self::isLoggedIn()) return false;

        $loggedRole = self::getLoggedRole();
        $adminRoles = array('owner', 'admin');
        if(in_array($loggedRole, $adminRoles)){
            return true;
        }
        return false;
    }

    /**
     * Checks if user is a guest (not logged in)
     * @return bool
     */
    public static function isGuest()
    {
        return (!self::isLoggedIn()) ? true : false;
    }

    /**
     * Handles access for non-logged users (block access)
     * @param string $location
     * @return string
     */
    public static function handleLogin($location = '')
    {
        if(APP_MODE == 'test') return '';
		
        if(!self::isLoggedIn()){
            if ($location == 'index/index') {
				header('location: '.CrypticBrain::app()->getRequest()->getBaseUrl());
				exit;
			}
			
			header('location: '.CrypticBrain::app()->getRequest()->getBaseUrl().$location);
            exit;
        }
    }

    /**
     * Handles access for logged in users (redirect logged in users)
     * @param string $location
     * @return string
     */
    public static function handleLoggedIn($location = '')
    {
        if(APP_MODE == 'test') return '';

        if(self::isLoggedIn()){
            if ($location == 'index/index') {
				header('location: '.CrypticBrain::app()->getRequest()->getBaseUrl());
				exit;
			}
			
			header('location: '.CrypticBrain::app()->getRequest()->getBaseUrl().$location);
            exit;
        }
    }

    /**
     * Returns ID of logged in user
     * @return string
     */
    public static function getLoggedId()
    {
        return (self::isLoggedIn()) ? CrypticBrain::app()->getSession()->get('loggedId') : null;
    }

    /**
     * Returns display name of logged in user
     * @return string
     */
    public static function getLoggedName()
    {
        return (self::isLoggedIn()) ? CrypticBrain::app()->getSession()->get('loggedName') : null;
    }

    /**
     * Returns email of logged in user
     * @return string
     */
    public static function getLoggedEmail()
    {
        return (self::isLoggedIn()) ? CrypticBrain::app()->getSession()->get('loggedEmail') : null;
    }

    /**
     * Returns role of logged in user
     * @return string
     */
    public static function getLoggedRole()
    {
        return (self::isLoggedIn()) ? CrypticBrain::app()->getSession()->get('loggedRole') : null;
    }

    /**
     * Returns parameter value of logged in user
     * @param string $param
     * @return string
     */
    public static function getLoggedParam($param)
    {
        $result = null;
        if(self::isLoggedIn() && CrypticBrain::app()->getSession()->isExists($param)){
            $result = CrypticBrain::app()->getSession()->get($param);
        }
        return $result;
    }
}