<?php
/**
 * CValidator is a helper class file that provides different validations methods
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * isEmpty
 * isAlpha
 * isNumeric
 * isAlphaNumeric
 * isMixed
 * isText
 * isPhone
 * isUsername
 * isEmail
 * isFileName
 * isDate
 * isDigit
 * isInteger
 * isFloat
 * isUrl
 * isValidMd5
 * inArray
 * validateLength
 * validateMinLength
 * validateMaxLength
 * validateRange
 * cleanString
 * detectEncoding
 *
 */
class CValidator
{
    /**
     * Checks if the given value is empty
     * @param mixed $value
     * @param boolean $trim
     * @return boolean whether the value is empty
     */
    public static function isEmpty($value, $trim = false)
    {
        return $value === null || $value === array() || $value === '' || ($trim && trim($value) === '');
    }

    /**
     * Checks if the given value is an alphabetic value
     * @param mixed $value
     * @return boolean
     */
    public static function isAlpha($value)
    {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }

    /**
     * Checks if the given value is a numeric value
     * @param mixed $value
     * @return boolean
     */
    public static function isNumeric($value)
    {
        return preg_match('/^[0-9]+$/', $value);
    }

    /**
     * Checks if the given value is a alpha-numeric value
     * @param mixed $value
     * @return boolean
     */
    public static function isAlphaNumeric($value)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    /**
     * Checks if the given value is a alpha-numeric value and underscores
     * @param mixed $value
     * @return boolean
     */
    public static function isMixed($value)
    {
        return preg_match('/^[A-Za-z0-9_-]+$/u', $value);
    }

    /**
     * Checks if the given value is a textual value and allowed HTML tags
     * @param mixed $value
     * @return boolean
     */
    public static function isText($value)
    {
        if((preg_match("/<[^>]*script*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*object*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*applet*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*meta*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*style*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*form*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*img*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*onmouseover*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*body*\"?[^>]*>/i", $value)) ||
            (preg_match("/\([^>]*\"?[^)]*\)/i", $value)) ||
            (preg_match("/ftp:\/\//i", $value)) ||
            (preg_match("/https:\/\//i", $value)) ||
            (preg_match("/http:\/\//i", $value)) )
        {
            return false;
        }
        return true;
    }

    /**
     * Checks if the given value is a phone number
     * @param mixed $value
     * @return boolean
     */
    public static function isPhone($value)
    {
        return preg_match('/^[+]{0,1}[\d]{3,12}[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,6}$/', $value);
    }

    /**
     * Checks if the given value is a username
     * @param mixed $value
     * @return boolean
     */
    public static function isUsername($value)
    {
        if(preg_match('/^[a-zA-Z0-9_\-]{6,20}$/', $value) && !self::isNumeric($value)){
            return true;
        }
        return false;
    }

    /**
     * Checks if the given value is an email
     * @param mixed $value
     * @return boolean
     */
    public static function isEmail($value)
    {
        return preg_match('/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/', $value);
    }

    /**
     * Checks if the given value is a file name
     * @param mixed $value
     * @return boolean
     */
    public static function isFileName($value)
    {
        return preg_match('/^[a-zA-Z0-9_\-]+$/', $value);
    }

    /**
     * Checks if the given value is a date value
     * @param mixed $value
     * @return boolean
     */
    public static function isDate($value)
    {
        $date = strtotime($value);
        return (!empty($date) && self::isInteger($date));
    }

    /**
     * Checks if the given value is a digit value
     * @param mixed $value
     * @return boolean
     */
    public static function isDigit($value)
    {
        return ctype_digit($value);
    }

    /**
     * Checks if the given value is an integer value
     * @param mixed $value
     * @return boolean
     */
    public static function isInteger($value)
    {
        return is_numeric($value) ? intval($value) == $value : false;
    }

    /**
     * Checks if the given value is a float value
     * @param mixed $value
     * @return boolean
     */
    public static function isFloat($value)
    {
        return is_numeric($value) ? floatval($value) == $value : false;
    }

    /**
     * Checks if the given value is a valid URL address
     * @param mixed $value
     * @return boolean
     */
    public static function isUrl($value)
    {
        return (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)) ? false : true;
    }

    /**
     * Validates value as IP address
     * @param mixed $value
     * @return boolean
     */
    public static function isIP($value)
    {
        return (!filter_var($value, FILTER_VALIDATE_IP)) ? false : true;
    }

    /**
     * Checks if the given value is valid md5
     * @param mixed $value
     * @return boolean
     */
    public static function isValidMd5($value)
    {
        return (strlen($value) == 32 and ctype_xdigit($value));
    }

    /**
     * Checks if the given value is hex color string
     * @param mixed $value
     * @param bool $hash
     * @return boolean
     */
    public static function isHexColor($value, $hash = true)
    {
        return ($hash===true ? preg_match('/^#[a-f0-9]{6}$/i', $value) : preg_match('/^[a-f0-9]{6}$/i', $value));
    }

    /**
     * Checks if the given value presents in a given array
     * @param mixed $value
     * @param array $array
     * @return boolean
     */
    public static function inArray($value, $array = array())
    {
        if(!is_array($array)) return false;
        return in_array($value, $array);
    }

    /**
     * Validates the length of the given value
     * @param string $value
     * @param integer $min
     * @param integer $max
     * @param boolean $encoding
     * @return boolean
     */
    public static function validateLength($value, $min, $max, $encoding = true)
    {
        $strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, CrypticBrain::app()->charset) : strlen($value);
        return ($strlen >= $min && $strlen <= $max);
    }

    /**
     * Validates the minimum length of the given value
     * @param string $value
     * @param integer $min
     * @param boolean $encoding
     * @return boolean
     */
    public static function validateMinLength($value, $min, $encoding = true)
    {
        $strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, CrypticBrain::app()->charset) : strlen($value);
        return ($strlen < $min) ? false : true;
    }

    /**
     * Validates the maximum length of the given value
     * @param string $value
     * @param integer $max
     * @param boolean $encoding
     * @return boolean
     */
    public static function validateMaxLength($value, $max, $encoding = true)
    {
        $strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, CrypticBrain::app()->charset) : strlen($value);
        return ($strlen > $max) ? false : true;
    }

    /**
     * Validates if the given numeric value in a specified range
     * @param string $value
     * @param integer $min
     * @param integer $max
     * @return boolean
     */
    public static function validateRange($value, $min, $max)
    {
        if(!is_numeric($value)) return false;
        return ($value >= $min && $value <= $max) ? true : false;
    }

    /**
     * Validates if the given numeric value in a specified range
     * @param string $value
     * @return boolean
     */
    public static function cleanString($value)
    {
        if(CrypticBrain::app()->charset == 'UTF-8'){
            return preg_replace('/[^a-zа-яё]+/iu', '', $value);
        }else{
            return preg_replace('/[^a-zа-яё]+/i', '', $value);
        }
    }

    /**
     * Detect character encoding
     * @param $str
     * @param null $encoding_list
     * @param bool $strict
     * @return string
     */
    public static function detectEncoding($str, $encoding_list = null, $strict = true)
    {
        return mb_detect_encoding($str, $encoding_list, $strict);
    }
}