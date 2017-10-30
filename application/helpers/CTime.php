<?php
/**
 * CTime is a helper class that provides a set of helper methods for timestamp operations
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * isValidDate
 * isValidTime
 * secondsToTime
 *
 */

class CTime
{
    const S = ' ';

    /**
     * Checks if the year, month and day are valid date value
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return boolean
     */
    public static function isValidDate($year, $month, $day)
    {
        return checkdate($month, $day, $year);
    }

    /**
     * Checks if the hour, minute and second are valid time value
     * @param integer $hour
     * @param integer $minute
     * @param integer $second
     * @return boolean
     */
    public static function isValidTime($hour, $minute, $second)
    {
        if($hour < 0 || $hour > 23) return false;
        if($minute  > 59 || $minute < 0) return false;
        if($second > 59 || $second < 0) return false;
        return true;
    }

    /**
     * Returns date from given seconds
     * @param int $ts
     * @param string $type
     * @param boolean $showTime
     * @return string
     */
    public static function makePretty($ts, $type = 'wide', $showTime = true)
    {
        if(!empty($ts)){
            $dt = new DateTime("@$ts");
            $dt->setTimezone(new DateTimeZone(CrypticBrain::app()->getTimezone()));
            $time = $dt->format('j');
            $time .= self::S.CrypticBrain::t('i18n', 'monthNames.'.$type.'.'.$dt->format('n'));
            $time .= self::S.$dt->format('Y');
            if($showTime){
                $time .= self::S.CrypticBrain::t('core', 'in').self::S.$dt->format('G:i');
            }
        }else{
            $time = CrypticBrain::t('core', 'Failed');
        }

        return $time;
    }

    /**
     * 	Returns time from given seconds
     * 	@param int $seconds
     * 	@return string
     */
    public static function convertSecondsToTime($seconds = 0)
    {
        if($seconds > 0){
            $dtf = new DateTime("@0");
            $dtt = new DateTime("@$seconds");
            $time = $dtf->diff($dtt)->format(
                '%a '.CrypticBrain::t('i18n', 'time.abbreviated.days')
                .'. %h '.CrypticBrain::t('i18n', 'time.abbreviated.hours')
                .'. %i '.CrypticBrain::t('i18n', 'time.abbreviated.minutes')
                .'. %s '.CrypticBrain::t('i18n', 'time.abbreviated.seconds')
            );
        }else{
            $time = CrypticBrain::t('core', 'none');
        }

        return $time;
    }
}