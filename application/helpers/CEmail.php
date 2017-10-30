<?php
/**
 * CEmail provides work with email address
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * shorten
 *
 */

class CEmail
{
    /**
     * Make short email
     * @param $email
     * @return mixed
     */
    public static function shorten($email)
    {
        if(CValidator::isEmail($email)){
            $parts = explode('@', $email);
            return substr($parts[0], 0, 2).'**@'.$parts[1];
        }
        return false;
    }

    /* todo: add more functions */
}