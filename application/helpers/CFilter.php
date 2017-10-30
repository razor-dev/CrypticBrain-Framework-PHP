<?php
/**
 * CFilter is a helper class file that provides different filters
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * sanitize
 *
 */

class CFilter
{
    /**
     * Sanitizes specified data
     * @param string $type
     * @param mixed $data
     * @return mixed
     */
    public static function sanitize($type, $data)
    {
        switch($type){
            case 'string':
                return filter_var($data, FILTER_SANITIZE_STRING);
                break;
            case 'email':
                return filter_var($data, FILTER_SANITIZE_EMAIL);
                break;
            case 'url':
                return filter_var($data, FILTER_SANITIZE_URL);
                break;
            case 'alpha':
                return preg_replace('/[^A-Za-z]/', '', $data);
                break;
            case 'alphanumeric':
                return preg_replace('/[^A-Za-z0-9]/', '', $data);
                break;
            case 'hour' || $type == 'minute':
                return preg_replace('/[^0-9]/', '', $data);
                break;
            case 'integer' || $type == 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case 'dbfield':
                return preg_replace('/[^A-Za-z0-9_\-]/', '', $data);
                break;
            default:
                return $data;
        }
    }
}